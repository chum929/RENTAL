import 'package:flutter/material.dart';
import '../../services/auth_service.dart';
import '../../services/city_service.dart';
import '../../models/city_model.dart';
import '../user/home_screen.dart';
import '../owner/owner_home_screen.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _nameCtrl     = TextEditingController();
  final _emailCtrl    = TextEditingController();
  final _phoneCtrl    = TextEditingController();
  final _passCtrl     = TextEditingController();
  final _confirmCtrl  = TextEditingController();

  // Untuk owner
  final _businessCtrl = TextEditingController();
  final _addressCtrl  = TextEditingController();

  String _role = 'user';
  int? _selectedCityId;
  List<CityModel> _cities = [];
  bool _isLoading = false;
  bool _obscurePass = true;
  String? _errorMsg;

  @override
  void initState() {
    super.initState();
    _loadCities();
  }

  Future<void> _loadCities() async {
    final cities = await CityService.getCities();
    setState(() => _cities = cities);
  }

  Future<void> _register() async {
    // Validasi dasar
    if (_nameCtrl.text.isEmpty || _emailCtrl.text.isEmpty ||
        _phoneCtrl.text.isEmpty || _passCtrl.text.isEmpty) {
      setState(() => _errorMsg = 'Semua field wajib diisi.');
      return;
    }
    if (_passCtrl.text != _confirmCtrl.text) {
      setState(() => _errorMsg = 'Password tidak cocok.');
      return;
    }
    if (_role == 'owner' &&
        (_businessCtrl.text.isEmpty || _selectedCityId == null || _addressCtrl.text.isEmpty)) {
      setState(() => _errorMsg = 'Lengkapi data rental terlebih dahulu.');
      return;
    }

    setState(() { _isLoading = true; _errorMsg = null; });

    final result = await AuthService.register(
      name:                 _nameCtrl.text,
      email:                _emailCtrl.text,
      phone:                _phoneCtrl.text,
      password:             _passCtrl.text,
      passwordConfirmation: _confirmCtrl.text,
      role:                 _role,
      businessName: _role == 'owner' ? _businessCtrl.text : null,
      cityId:       _role == 'owner' ? _selectedCityId : null,
      address:      _role == 'owner' ? _addressCtrl.text : null,
    );

    setState(() => _isLoading = false);
    if (!mounted) return;

    if (result['success']) {
      // Update provider
      await context.read<AuthProvider>().checkAuth();
      if (!mounted) return;
      if (_role == 'owner') {
        Navigator.pushReplacement(
            context, MaterialPageRoute(builder: (_) => const OwnerHomeScreen()));
      } else {
        Navigator.pushReplacement(
            context, MaterialPageRoute(builder: (_) => const HomeScreen()));
      }
    } else {
      setState(() => _errorMsg = result['message'] ?? 'Registrasi gagal.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Buat Akun')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Pilih Role
            const Text('Daftar sebagai', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 15)),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(child: _roleCard('user',  '👤', 'Penyewa',  'Sewa mobil')),
                const SizedBox(width: 12),
                Expanded(child: _roleCard('owner', '🏢', 'Owner',    'Sewakan mobil')),
              ],
            ),
            const SizedBox(height: 20),

            // Error
            if (_errorMsg != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 14),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  border: Border.all(color: Colors.red.shade200),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(_errorMsg!, style: TextStyle(color: Colors.red.shade700, fontSize: 13)),
              ),

            _buildField('Nama Lengkap', _nameCtrl, hint: 'Nama kamu'),
            _buildField('Email', _emailCtrl, hint: 'email@example.com',
                keyboardType: TextInputType.emailAddress),
            _buildField('Nomor HP', _phoneCtrl, hint: '08xxxxxxxx',
                keyboardType: TextInputType.phone),

            // Field khusus owner
            if (_role == 'owner') ...[
              const Divider(height: 28),
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.blue.shade50,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('📋 Informasi Rental',
                        style: TextStyle(fontWeight: FontWeight.w600, color: Color(0xFF1E40AF))),
                    const SizedBox(height: 12),
                    _buildField('Nama Usaha Rental', _businessCtrl, hint: 'Budi Car Rental'),
                    // Pilih kota
                    const Text('Kota', style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
                    const SizedBox(height: 6),
                    DropdownButtonFormField<int>(
                      value: _selectedCityId,
                      hint: const Text('Pilih kota'),
                      decoration: _inputDeco(),
                      items: _cities.map((c) => DropdownMenuItem(
                        value: c.id, child: Text(c.name))).toList(),
                      onChanged: (v) => setState(() => _selectedCityId = v),
                    ),
                    const SizedBox(height: 14),
                    _buildField('Alamat Rental', _addressCtrl,
                        hint: 'Jl. Contoh No. 1', maxLines: 2),
                  ],
                ),
              ),
              const SizedBox(height: 14),
            ],

            // Password
            const Text('Password', style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
            const SizedBox(height: 6),
            TextField(
              controller: _passCtrl,
              obscureText: _obscurePass,
              decoration: _inputDeco(
                hint: 'Minimal 6 karakter',
                suffix: IconButton(
                  icon: Icon(_obscurePass ? Icons.visibility_off : Icons.visibility, size: 20),
                  onPressed: () => setState(() => _obscurePass = !_obscurePass),
                ),
              ),
            ),
            const SizedBox(height: 14),
            _buildField('Konfirmasi Password', _confirmCtrl,
                hint: 'Ulangi password', obscure: true),
            const SizedBox(height: 24),

            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _register,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: _isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Daftar Sekarang', style: TextStyle(fontSize: 16)),
              ),
            ),
            const SizedBox(height: 16),
            Center(
              child: TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text.rich(TextSpan(
                  text: 'Sudah punya akun? ',
                  style: TextStyle(color: Colors.grey),
                  children: [TextSpan(text: 'Login',
                      style: TextStyle(color: Color(0xFF2563EB), fontWeight: FontWeight.w600))],
                )),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _roleCard(String value, String icon, String title, String sub) {
    final selected = _role == value;
    return GestureDetector(
      onTap: () => setState(() => _role = value),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 12),
        decoration: BoxDecoration(
          color: selected ? const Color(0xFFEFF6FF) : Colors.grey.shade50,
          border: Border.all(
              color: selected ? const Color(0xFF2563EB) : Colors.grey.shade200,
              width: selected ? 2 : 1),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Column(children: [
          Text(icon, style: const TextStyle(fontSize: 28)),
          const SizedBox(height: 4),
          Text(title, style: TextStyle(fontWeight: FontWeight.bold,
              color: selected ? const Color(0xFF1E40AF) : Colors.grey.shade800)),
          Text(sub, style: TextStyle(fontSize: 11,
              color: selected ? const Color(0xFF3B82F6) : Colors.grey)),
        ]),
      ),
    );
  }

  Widget _buildField(String label, TextEditingController ctrl,
      {String? hint, TextInputType? keyboardType, int maxLines = 1, bool obscure = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
        const SizedBox(height: 6),
        TextField(
          controller: ctrl,
          keyboardType: keyboardType,
          maxLines: maxLines,
          obscureText: obscure,
          decoration: _inputDeco(hint: hint),
        ),
        const SizedBox(height: 14),
      ],
    );
  }

  InputDecoration _inputDeco({String? hint, Widget? suffix}) {
    return InputDecoration(
      hintText: hint,
      suffixIcon: suffix,
      filled: true,
      fillColor: Colors.grey.shade50,
      border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey.shade200)),
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey.shade200)),
      focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF2563EB))),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
    );
  }
}