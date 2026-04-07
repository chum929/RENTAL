import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../providers/auth_provider.dart';
import '../../services/auth_service.dart';
import '../../config/api_config.dart';
import '../auth/login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _nameCtrl  = TextEditingController();
  final _phoneCtrl = TextEditingController();
  bool _isEditing  = false;
  bool _isSaving   = false;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthProvider>().user;
    _nameCtrl.text  = user?.name ?? '';
    _phoneCtrl.text = user?.phone ?? '';
  }

  Future<void> _saveProfile() async {
    setState(() => _isSaving = true);
    final headers = await AuthService.authHeaders();
    final response = await http.put(
      Uri.parse('${ApiConfig.baseUrl}/me'),
      headers: headers,
      body: jsonEncode({'name': _nameCtrl.text, 'phone': _phoneCtrl.text}),
    );
    setState(() { _isSaving = false; _isEditing = false; });
    if (!mounted) return;
    if (response.statusCode == 200) {
      await context.read<AuthProvider>().checkAuth();
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Profil diperbarui!')));
    }
  }

  Future<void> _pickAndUploadPhoto() async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.gallery, imageQuality: 80);
    if (picked == null) return;

    final token = await AuthService.getToken();
    final request = http.MultipartRequest(
      'POST', Uri.parse('${ApiConfig.baseUrl}/me/photo'));
    request.headers['Authorization'] = 'Bearer $token';
    request.headers['Accept']        = 'application/json';
    request.files.add(await http.MultipartFile.fromPath('photo', picked.path));

    final streamed = await request.send();
    if (!mounted) return;
    if (streamed.statusCode == 200) {
      await context.read<AuthProvider>().checkAuth();
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Foto profil diperbarui!')));
    }
  }

  Future<void> _logout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Logout?'),
        content: const Text('Kamu yakin ingin keluar?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Logout', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
    if (confirm == true && mounted) {
      await context.read<AuthProvider>().logout();
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
        (_) => false,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Profil Saya'),
        actions: [
          TextButton(
            onPressed: () {
              if (_isEditing) {
                _saveProfile();
              } else {
                setState(() => _isEditing = true);
              }
            },
            child: Text(_isEditing ? 'Simpan' : 'Edit',
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Avatar
            Center(
              child: Stack(
                children: [
                  CircleAvatar(
                    radius: 52,
                    backgroundColor: Colors.blue.shade100,
                    backgroundImage: user?.photo != null
                        ? NetworkImage('${ApiConfig.storageUrl}/${user!.photo}')
                        : null,
                    child: user?.photo == null
                        ? Text(
                            user?.name.isNotEmpty == true
                                ? user!.name[0].toUpperCase()
                                : '?',
                            style: const TextStyle(
                                fontSize: 36, fontWeight: FontWeight.bold,
                                color: Color(0xFF2563EB)),
                          )
                        : null,
                  ),
                  Positioned(
                    bottom: 0, right: 0,
                    child: GestureDetector(
                      onTap: _pickAndUploadPhoto,
                      child: Container(
                        padding: const EdgeInsets.all(7),
                        decoration: const BoxDecoration(
                          color: Color(0xFF2563EB), shape: BoxShape.circle),
                        child: const Icon(Icons.camera_alt, color: Colors.white, size: 16),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 8),
            Text(user?.name ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            Container(
              margin: const EdgeInsets.only(top: 4),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: Colors.blue.shade50,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                user?.role == 'owner' ? '🏢 Owner' : '👤 Penyewa',
                style: TextStyle(color: Colors.blue.shade700, fontSize: 13, fontWeight: FontWeight.w500),
              ),
            ),
            const SizedBox(height: 28),

            // Form edit profil
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 6)],
              ),
              child: Column(
                children: [
                  _profileField('Nama Lengkap', _nameCtrl, enabled: _isEditing),
                  const Divider(height: 1),
                  _profileField('Nomor HP', _phoneCtrl,
                      enabled: _isEditing, keyboardType: TextInputType.phone),
                  const Divider(height: 1),
                  // Email (tidak bisa diedit)
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    child: Row(
                      children: [
                        const Icon(Icons.email_outlined, color: Colors.grey, size: 20),
                        const SizedBox(width: 12),
                        Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Email', style: TextStyle(fontSize: 12, color: Colors.grey)),
                          Text(user?.email ?? '', style: const TextStyle(fontWeight: FontWeight.w500)),
                        ]),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 28),

            // Tombol Logout
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: _logout,
                icon: const Icon(Icons.logout, color: Colors.red),
                label: const Text('Logout', style: TextStyle(color: Colors.red, fontSize: 15)),
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Colors.red),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _profileField(String label, TextEditingController ctrl,
      {bool enabled = false, TextInputType? keyboardType}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 14),
      child: Row(
        children: [
          Icon(
            label == 'Nama Lengkap' ? Icons.person_outline : Icons.phone_outlined,
            color: Colors.grey, size: 20,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
                enabled
                    ? TextField(
                        controller: ctrl,
                        keyboardType: keyboardType,
                        decoration: const InputDecoration(
                          isDense: true,
                          contentPadding: EdgeInsets.zero,
                          border: InputBorder.none,
                        ),
                        style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 15),
                      )
                    : Text(ctrl.text, style: const TextStyle(fontWeight: FontWeight.w500)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}