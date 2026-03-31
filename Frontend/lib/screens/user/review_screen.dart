import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import '../../models/booking_model.dart';
import '../../services/auth_service.dart';
import '../../config/api_config.dart';

class ReviewScreen extends StatefulWidget {
  final BookingModel booking;
  const ReviewScreen({super.key, required this.booking});

  @override
  State<ReviewScreen> createState() => _ReviewScreenState();
}

class _ReviewScreenState extends State<ReviewScreen> {
  int _rating = 5;
  final _commentCtrl = TextEditingController();
  bool _isLoading    = false;

  Future<void> _submit() async {
    setState(() => _isLoading = true);
    final headers = await AuthService.authHeaders();
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/reviews'),
      headers: headers,
      body: jsonEncode({
        'booking_id': widget.booking.id,
        'rating':     _rating,
        'comment':    _commentCtrl.text,
      }),
    );
    setState(() => _isLoading = false);
    if (!mounted) return;
    if (response.statusCode == 201) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(
              content: Text('⭐ Review berhasil dikirim!'),
              backgroundColor: Colors.green));
    } else {
      final data = jsonDecode(response.body);
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text(data['message'] ?? 'Gagal kirim review')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Beri Review')),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            const SizedBox(height: 10),
            Text(widget.booking.car?.rentalProvider?.businessName ?? 'Rental',
                style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 6),
            Text('Booking ${widget.booking.car?.name ?? ''}',
                style: TextStyle(color: Colors.grey.shade600)),
            const SizedBox(height: 28),

            const Text('Beri Penilaian', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16)),
            const SizedBox(height: 12),
            // Bintang
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(5, (i) {
                return GestureDetector(
                  onTap: () => setState(() => _rating = i + 1),
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                    child: Icon(
                      i < _rating ? Icons.star_rounded : Icons.star_outline_rounded,
                      size: 44,
                      color: i < _rating ? const Color(0xFFF59E0B) : Colors.grey.shade300,
                    ),
                  ),
                );
              }),
            ),
            const SizedBox(height: 6),
            Text(
              switch (_rating) {
                1 => 'Sangat Buruk',
                2 => 'Buruk',
                3 => 'Cukup',
                4 => 'Bagus',
                5 => 'Sangat Bagus!',
                _ => '',
              },
              style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15),
            ),
            const SizedBox(height: 24),

            TextField(
              controller: _commentCtrl,
              maxLines: 4,
              decoration: InputDecoration(
                hintText: 'Tulis ulasanmu di sini...',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                filled: true,
                fillColor: Colors.grey.shade50,
              ),
            ),
            const SizedBox(height: 24),

            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _submit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFF59E0B),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: _isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Kirim Review', style: TextStyle(fontSize: 16)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}