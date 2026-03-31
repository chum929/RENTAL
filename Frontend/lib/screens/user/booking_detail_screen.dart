import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/booking_model.dart';
import '../../services/booking_service.dart';
import '../../config/api_config.dart';
import 'review_screen.dart';

class BookingDetailScreen extends StatefulWidget {
  final int bookingId;
  const BookingDetailScreen({super.key, required this.bookingId});

  @override
  State<BookingDetailScreen> createState() => _BookingDetailScreenState();
}

class _BookingDetailScreenState extends State<BookingDetailScreen> {
  BookingModel? _booking;
  bool _isLoading = true;

  final currency   = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
  final dateFormat = DateFormat('dd MMMM yyyy', 'id_ID');

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final b = await BookingService.getBookingDetail(widget.bookingId);
    setState(() { _booking = b; _isLoading = false; });
  }

  Future<void> _cancel() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Batalkan Booking?'),
        content: const Text('Apakah kamu yakin ingin membatalkan booking ini?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Tidak')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Ya, Batalkan', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
    if (confirm == true) {
      final ok = await BookingService.cancelBooking(widget.bookingId);
      if (!mounted) return;
      if (ok) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context)
            .showSnackBar(const SnackBar(content: Text('Booking dibatalkan.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    if (_booking == null) return const Scaffold(body: Center(child: Text('Booking tidak ditemukan')));

    final b = _booking!;

    Color statusColor;
    switch (b.status) {
      case 'pending':   statusColor = Colors.orange; break;
      case 'approved':  statusColor = Colors.green;  break;
      case 'rejected':  statusColor = Colors.red;    break;
      case 'completed': statusColor = Colors.blue;   break;
      default:          statusColor = Colors.grey;
    }

    return Scaffold(
      appBar: AppBar(title: Text('Booking #${b.id}')),
      backgroundColor: Colors.grey.shade100,
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // Status badge besar
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: statusColor.withOpacity(0.3)),
              ),
              child: Column(children: [
                Text(
                  switch (b.status) {
                    'pending'   => '⏳',
                    'approved'  => '✅',
                    'rejected'  => '❌',
                    'completed' => '🎉',
                    'cancelled' => '🚫',
                    _           => '📋',
                  },
                  style: const TextStyle(fontSize: 36),
                ),
                const SizedBox(height: 6),
                Text(b.statusLabel,
                    style: TextStyle(
                        color: statusColor, fontWeight: FontWeight.bold, fontSize: 18)),
                if (b.isPending)
                  Text('Menunggu konfirmasi dari penyedia rental',
                      style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
              ]),
            ),
            const SizedBox(height: 14),

            // Info Mobil
            _card(
              children: [
                _row('🚗 Mobil',    b.car?.name ?? '-'),
                _row('🏢 Rental',  b.car?.rentalProvider?.businessName ?? '-'),
                _row('📅 Mulai',   dateFormat.format(DateTime.parse(b.startDate))),
                _row('📅 Selesai', dateFormat.format(DateTime.parse(b.endDate))),
                _row('🌙 Durasi',  '${b.totalDays} hari'),
                const Divider(),
                _row('💰 Total',   currency.format(b.totalPrice), bold: true),
              ],
            ),
            const SizedBox(height: 12),

            // Catatan
            if (b.notes != null && b.notes!.isNotEmpty)
              _card(children: [
                const Text('Catatan:', style: TextStyle(fontWeight: FontWeight.bold)),
                const SizedBox(height: 6),
                Text(b.notes!, style: TextStyle(color: Colors.grey.shade700)),
              ]),

            const SizedBox(height: 20),

            // Tombol aksi
            if (b.isPending)
              SizedBox(
                width: double.infinity,
                child: OutlinedButton(
                  onPressed: _cancel,
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.red,
                    side: const BorderSide(color: Colors.red),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: const Text('Batalkan Booking', style: TextStyle(fontSize: 15)),
                ),
              ),

            // Tombol beri review jika sudah selesai
            if (b.isCompleted)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => ReviewScreen(booking: b)),
                  ).then((_) => _load()),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFF59E0B),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: const Text('⭐ Beri Review', style: TextStyle(fontSize: 15)),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _card({required List<Widget> children}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 6)],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: children,
      ),
    );
  }

  Widget _row(String label, String value, {bool bold = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
          Text(value, style: TextStyle(
              fontWeight: bold ? FontWeight.bold : FontWeight.w500,
              fontSize: bold ? 16 : 13,
              color: bold ? const Color(0xFF2563EB) : Colors.grey.shade800)),
        ],
      ),
    );
  }
}