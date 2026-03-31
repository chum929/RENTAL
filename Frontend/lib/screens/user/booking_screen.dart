import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/car_model.dart';
import '../../services/booking_service.dart';
import '../../config/api_config.dart';
import 'my_bookings_screen.dart';

class BookingScreen extends StatefulWidget {
  final CarModel car;
  const BookingScreen({super.key, required this.car});

  @override
  State<BookingScreen> createState() => _BookingScreenState();
}

class _BookingScreenState extends State<BookingScreen> {
  DateTime? _startDate;
  DateTime? _endDate;
  final _notesCtrl = TextEditingController();
  bool _isLoading  = false;
  String? _errorMsg;

  final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  int get _totalDays {
    if (_startDate == null || _endDate == null) return 0;
    return _endDate!.difference(_startDate!).inDays;
  }

  double get _totalPrice => _totalDays * widget.car.pricePerDay;

  Future<void> _pickDate(bool isStart) async {
    final now  = DateTime.now();
    final first = isStart ? now : (_startDate?.add(const Duration(days: 1)) ?? now);

    final picked = await showDatePicker(
      context: context,
      initialDate: first,
      firstDate: first,
      lastDate: now.add(const Duration(days: 365)),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: const ColorScheme.light(primary: Color(0xFF2563EB)),
        ),
        child: child!,
      ),
    );

    if (picked != null) {
      setState(() {
        if (isStart) {
          _startDate = picked;
          // Reset end date jika lebih awal dari start
          if (_endDate != null && _endDate!.isBefore(picked.add(const Duration(days: 1)))) {
            _endDate = null;
          }
        } else {
          _endDate = picked;
        }
      });
    }
  }

  Future<void> _submitBooking() async {
    if (_startDate == null || _endDate == null) {
      setState(() => _errorMsg = 'Pilih tanggal mulai dan selesai terlebih dahulu.');
      return;
    }
    if (_totalDays < 1) {
      setState(() => _errorMsg = 'Minimal sewa 1 hari.');
      return;
    }

    setState(() { _isLoading = true; _errorMsg = null; });

    final result = await BookingService.createBooking(
      carId:     widget.car.id,
      startDate: DateFormat('yyyy-MM-dd').format(_startDate!),
      endDate:   DateFormat('yyyy-MM-dd').format(_endDate!),
      notes:     _notesCtrl.text,
    );

    setState(() => _isLoading = false);
    if (!mounted) return;

    if (result['success']) {
      // Tampilkan sukses lalu ke halaman booking saya
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (_) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Text('🎉 Booking Berhasil!'),
          content: const Text(
              'Booking kamu sudah dikirim. Tunggu konfirmasi dari penyedia rental ya.'),
          actions: [
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context); // tutup dialog
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (_) => const MyBookingsScreen()),
                  (route) => route.isFirst,
                );
              },
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF2563EB)),
              child: const Text('Lihat Booking Saya', style: TextStyle(color: Colors.white)),
            ),
          ],
        ),
      );
    } else {
      setState(() => _errorMsg = result['message'] ?? 'Booking gagal, coba lagi.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');

    return Scaffold(
      appBar: AppBar(title: const Text('Form Booking')),
      backgroundColor: Colors.grey.shade100,
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Kartu ringkasan mobil
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 6)],
              ),
              child: Row(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(10),
                    child: widget.car.photo != null
                        ? Image.network(
                            '${ApiConfig.storageUrl}/${widget.car.photo}',
                            width: 80, height: 70, fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => _photoPlaceholder(),
                          )
                        : _photoPlaceholder(),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(widget.car.name,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                      Text('${widget.car.type} · ${widget.car.seats} kursi',
                          style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
                      const SizedBox(height: 4),
                      Text(currency.format(widget.car.pricePerDay) + '/hari',
                          style: const TextStyle(
                              color: Color(0xFF2563EB), fontWeight: FontWeight.bold)),
                    ]),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Error message
            if (_errorMsg != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 12),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  border: Border.all(color: Colors.red.shade200),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(_errorMsg!, style: TextStyle(color: Colors.red.shade700, fontSize: 13)),
              ),

            // Pilih Tanggal
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [BoxShadow(
                    color: Colors.black.withOpacity(0.05), blurRadius: 6)],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('📅 Pilih Tanggal Sewa',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      Expanded(child: _datePicker(
                        label: 'Tanggal Mulai',
                        value: _startDate != null ? dateFormat.format(_startDate!) : null,
                        onTap: () => _pickDate(true),
                      )),
                      Container(
                        margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 20),
                        child: const Icon(Icons.arrow_forward, color: Colors.grey),
                      ),
                      Expanded(child: _datePicker(
                        label: 'Tanggal Selesai',
                        value: _endDate != null ? dateFormat.format(_endDate!) : null,
                        onTap: _startDate == null ? null : () => _pickDate(false),
                      )),
                    ],
                  ),

                  // Ringkasan harga jika tanggal sudah dipilih
                  if (_totalDays > 0) ...[
                    const Divider(height: 24),
                    _priceRow('Durasi', '$_totalDays hari'),
                    _priceRow('Harga per hari', currency.format(widget.car.pricePerDay)),
                    const Divider(height: 16),
                    _priceRow('Total Harga', currency.format(_totalPrice),
                        isBold: true, color: const Color(0xFF2563EB)),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 14),

            // Catatan
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [BoxShadow(
                    color: Colors.black.withOpacity(0.05), blurRadius: 6)],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('📝 Catatan (opsional)',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                  const SizedBox(height: 10),
                  TextField(
                    controller: _notesCtrl,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText: 'Misal: butuh kursi bayi, jemput di stasiun, dll...',
                      hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                      border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                          borderSide: BorderSide(color: Colors.grey.shade200)),
                      enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                          borderSide: BorderSide(color: Colors.grey.shade200)),
                      filled: true,
                      fillColor: Colors.grey.shade50,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Tombol submit
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _submitBooking,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(13)),
                ),
                child: _isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('✅ Kirim Booking',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ),
            ),
            const SizedBox(height: 8),
            Center(
              child: Text('Booking bisa dibatalkan selama masih pending',
                  style: TextStyle(color: Colors.grey.shade500, fontSize: 12)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _datePicker({required String label, String? value, VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.grey.shade50,
          border: Border.all(color: Colors.grey.shade200),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(label, style: TextStyle(color: Colors.grey.shade500, fontSize: 11)),
          const SizedBox(height: 4),
          Text(
            value ?? 'Pilih tanggal',
            style: TextStyle(
              fontWeight: value != null ? FontWeight.bold : FontWeight.normal,
              color: value != null ? Colors.grey.shade800 : Colors.grey.shade400,
              fontSize: 13,
            ),
          ),
        ]),
      ),
    );
  }

  Widget _priceRow(String label, String value,
      {bool isBold = false, Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 3),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label,
              style: TextStyle(
                  color: Colors.grey.shade600, fontWeight: isBold ? FontWeight.bold : FontWeight.normal)),
          Text(value,
              style: TextStyle(
                  fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
                  color: color ?? Colors.grey.shade800,
                  fontSize: isBold ? 16 : 14)),
        ],
      ),
    );
  }

  Widget _photoPlaceholder() => Container(
    width: 80, height: 70, color: Colors.grey.shade200,
    child: const Icon(Icons.directions_car, color: Colors.grey),
  );
}