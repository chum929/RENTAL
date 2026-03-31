import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/booking_model.dart';
import '../../services/booking_service.dart';

class OwnerBookingsScreen extends StatefulWidget {
  const OwnerBookingsScreen({super.key});

  @override
  State<OwnerBookingsScreen> createState() => _OwnerBookingsScreenState();
}

class _OwnerBookingsScreenState extends State<OwnerBookingsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabCtrl;
  List<BookingModel> _bookings = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabCtrl = TabController(length: 4, vsync: this);
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    final bookings = await BookingService.getOwnerBookings();
    setState(() { _bookings = bookings; _isLoading = false; });
  }

  List<BookingModel> _filtered(String status) {
    if (status == 'all') return _bookings;
    return _bookings.where((b) => b.status == status).toList();
  }

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Booking Masuk'),
        bottom: TabBar(
          controller: _tabCtrl,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          indicatorColor: Colors.white,
          tabs: const [
            Tab(text: 'Semua'),
            Tab(text: 'Pending'),
            Tab(text: 'Aktif'),
            Tab(text: 'Selesai'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: TabBarView(
                controller: _tabCtrl,
                children: [
                  _buildList('all', currency),
                  _buildList('pending', currency),
                  _buildList('approved', currency),
                  _buildList('completed', currency),
                ],
              ),
            ),
    );
  }

  Widget _buildList(String status, NumberFormat currency) {
    final list = _filtered(status);
    if (list.isEmpty) {
      return const Center(child: Text('Tidak ada booking.', style: TextStyle(color: Colors.grey)));
    }
    return ListView.builder(
      padding: const EdgeInsets.all(14),
      itemCount: list.length,
      itemBuilder: (_, i) {
        final b = list[i];
        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [BoxShadow(
                color: Colors.black.withOpacity(0.04), blurRadius: 6)],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(b.user?.name ?? '-',
                      style: const TextStyle(fontWeight: FontWeight.bold)),
                  _statusBadge(b.status),
                ],
              ),
              const SizedBox(height: 6),
              Text('🚗 ${b.car?.name ?? '-'}',
                  style: TextStyle(color: Colors.grey.shade700)),
              Text('📅 ${b.startDate} → ${b.endDate}',
                  style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
              Text('📞 ${b.user?.phone ?? '-'}',
                  style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(currency.format(b.totalPrice),
                      style: const TextStyle(
                          fontWeight: FontWeight.bold, color: Color(0xFF2563EB), fontSize: 15)),
                  if (b.isPending)
                    Row(children: [
                      OutlinedButton(
                        onPressed: () async {
                          await BookingService.rejectBooking(b.id);
                          _load();
                        },
                        style: OutlinedButton.styleFrom(
                          foregroundColor: Colors.red,
                          side: const BorderSide(color: Colors.red),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          minimumSize: Size.zero,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                        ),
                        child: const Text('Tolak', style: TextStyle(fontSize: 12)),
                      ),
                      const SizedBox(width: 8),
                      ElevatedButton(
                        onPressed: () async {
                          await BookingService.approveBooking(b.id);
                          _load();
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          minimumSize: Size.zero,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                        ),
                        child: const Text('Terima', style: TextStyle(fontSize: 12)),
                      ),
                    ]),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _statusBadge(String status) {
    final map = {
      'pending':   [Colors.orange, 'Pending'],
      'approved':  [Colors.green,  'Disetujui'],
      'rejected':  [Colors.red,    'Ditolak'],
      'completed': [Colors.blue,   'Selesai'],
    };
    final color = (map[status]?[0] as Color?) ?? Colors.grey;
    final label = (map[status]?[1] as String?) ?? status;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(label, style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.bold)),
    );
  }
}