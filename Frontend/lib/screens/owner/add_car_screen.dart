import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../../services/car_service.dart';

class AddCarScreen extends StatefulWidget {
  const AddCarScreen({super.key});

  @override
  State<AddCarScreen> createState() => _AddCarScreenState();
}

class _AddCarScreenState extends State<AddCarScreen> {
  final _nameCtrl  = TextEditingController();
  final _plateCtrl = TextEditingController();
  final _descCtrl  = TextEditingController();
  final _priceCtrl = TextEditingController();

  String _type    = 'MPV';
  int _year       = DateTime.now().year;
  int _seats      = 5;
  File? _photo;
  bool _isLoading = false;
  String? _errorMsg;

  final _types = ['MPV', 'SUV', 'Sedan', 'City Car', 'Pickup', 'Minibus'];

  Future<void> _pickPhoto() async {
    final picked = await ImagePicker().pickImage(
        source: ImageSource.gallery, imageQuality: 80);
    if (picked != null) setState(() => _photo = File(picked.path));
  }

  Future<void> _submit() async {
    if (_nameCtrl.text.isEmpty || _plateCtrl.text.isEmpty || _priceCtrl.text.isEmpty) {
      setState(() => _errorMsg = 'Nama, plat nomor, dan harga wajib diisi.');
      return;
    }
    setState(() { _isLoading = true; _errorMsg = null; });

    final result = await CarService.addCar(
      name:        _nameCtrl.text,
      type:        _type,
      plateNumber: _plateCtrl.text,
      year:        _year,
      seats:       _seats,
      pricePerDay: double.tryParse(_priceCtrl.text) ?? 0,
      description: _descCtrl.text,
      photoPath:   _photo?.path,
    );

    setState(() => _isLoading = false);
    if (!mounted) return;

    if (result['success']) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(
              content: Text('🚗 Mobil berhasil ditambahkan!'),
              backgroundColor: Colors.green));
    } else {
      setState(() => _errorMsg = result['message'] ?? 'Gagal menambahkan mobil.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Tambah Mobil')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Upload foto
            GestureDetector(
              onTap: _pickPhoto,
              child: Container(
                width: double.infinity,
                height: 160,
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.shade300, style: BorderStyle.solid),
                ),
                child: _photo != null
                    ? ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: Image.file(_photo!, fit: BoxFit.cover))
                    : Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.add_photo_alternate_outlined,
                              size: 42, color: Colors.grey.shade400),
                          const SizedBox(height: 8),
                          Text('Tap untuk upload foto mobil',
                              style: TextStyle(color: Colors.grey.shade500)),
                        ],
                      ),
              ),
            ),
            const SizedBox(height: 16),

            if (_errorMsg != null)
              Container(
                padding: const EdgeInsets.all(10),
                margin: const EdgeInsets.only(bottom: 12),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(_errorMsg!, style: TextStyle(color: Colors.red.shade700, fontSize: 13)),
              ),

            _field('Nama Mobil', _nameCtrl, hint: 'Contoh: Toyota Avanza'),
            _field('Plat Nomor', _plateCtrl, hint: 'Contoh: B 1234 ABC'),

            // Dropdown tipe
            const Text('Tipe Mobil', style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
            const SizedBox(height: 6),
            DropdownButtonFormField<String>(
              initialValue: _type,
              decoration: _deco(),
              items: _types.map((t) => DropdownMenuItem(value: t, child: Text(t))).toList(),
              onChanged: (v) => setState(() => _type = v!),
            ),
            const SizedBox(height: 14),

            // Tahun & Kursi dalam satu baris
            Row(children: [
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Tahun', style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
                  const SizedBox(height: 6),
                  DropdownButtonFormField<int>(
                    initialValue: _year,
                    decoration: _deco(),
                    items: List.generate(15, (i) => DateTime.now().year - i)
                        .map((y) => DropdownMenuItem(value: y, child: Text('$y'))).toList(),
                    onChanged: (v) => setState(() => _year = v!),
                  ),
                ]),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Jumlah Kursi', style: TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
                  const SizedBox(height: 6),
                  DropdownButtonFormField<int>(
                    initialValue: _seats,
                    decoration: _deco(),
                    items: [2, 4, 5, 6, 7, 8, 9, 10, 12]
                        .map((s) => DropdownMenuItem(value: s, child: Text('$s kursi'))).toList(),
                    onChanged: (v) => setState(() => _seats = v!),
                  ),
                ]),
              ),
            ]),
            const SizedBox(height: 14),

            _field('Harga per Hari (Rp)', _priceCtrl,
                hint: 'Contoh: 350000', keyboardType: TextInputType.number),
            _field('Deskripsi (opsional)', _descCtrl,
                hint: 'Kondisi mobil, fasilitas, dll...', maxLines: 3),

            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _submit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: _isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Simpan Mobil', style: TextStyle(fontSize: 16)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _field(String label, TextEditingController ctrl,
      {String? hint, TextInputType? keyboardType, int maxLines = 1}) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
      const SizedBox(height: 6),
      TextField(
        controller: ctrl,
        keyboardType: keyboardType,
        maxLines: maxLines,
        decoration: _deco(hint: hint),
      ),
      const SizedBox(height: 14),
    ]);
  }

  InputDecoration _deco({String? hint}) => InputDecoration(
    hintText: hint,
    filled: true,
    fillColor: Colors.grey.shade50,
    border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide(color: Colors.grey.shade200)),
    enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide(color: Colors.grey.shade200)),
    contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
  );
}