import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/booking_model.dart';
import '../../services/booking_service.dart';
import '../../config/api_config.dart';
import 'booking_detail_screen.dart';

class MyBookingsScreen extends StatefulWidget {
  const MyBookingsScreen({super.key});

  @override
  State<MyBookingsScreen> createState() => _MyBookingsScreenState();
}

class _MyBookingsScreenState extends State<MyBookingsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabCtrl;
  List<BookingModel> _allBookings = [];
  bool _isLoading = true;

  final _tabs = ['Semua', 'Pending', 'Aktif', 'Selesai', 'Ditolak'];

  @override
  void initState() {
    super.initState();
    _tabCtrl = TabController(length: _tabs.length, vsync: this);
    _loadBookings();
  }

  Future<void> _loadBookings() async {
    setState(() => _isLoading = true);
    final bookings = await BookingService.getMyBookings();
    setState(() { _allBookings = bookings; _isLoading = false; });
  }

  List<BookingModel> _filtered(int tabIndex) {
    if (tabIndex == 0) return _allBookings;
    final map = {1: 'pending', 2: 'approved', 3: 'completed', 4: 'rejected'};
    return _allBookings.where((b) => b.status == map[tabIndex]).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Booking Saya'),
        bottom: TabBar(
          controller: _tabCtrl,
          isScrollable: true,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          indicatorColor: Colors.white,
          tabs: _tabs.map((t) => Tab(text: t)).toList(),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadBookings,
              child: TabBarView(
                controller: _tabCtrl,
                children: List.generate(_tabs.length, (i) {
                  final list = _filtered(i);
                  if (list.isEmpty) {
                    return Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Text('📋', style: TextStyle(fontSize: 48)),
                          const SizedBox(height: 12),
                          Text('Tidak ada booking ${_tabs[i].toLowerCase()}',
                              style: TextStyle(color: Colors.grey.shade600)),
                        ],
                      ),
                    );
                  }
                  return ListView.builder(
                    padding: const EdgeInsets.all(14),
                    itemCount: list.length,
                    itemBuilder: (_, idx) => _BookingCard(
                      booking: list[idx],
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => BookingDetailScreen(bookingId: list[idx].id)),
                      ).then((_) => _loadBookings()),
                    ),
                  );
                }),
              ),
            ),
    );
  }
}

class _BookingCard extends StatelessWidget {
  final BookingModel booking;
  final VoidCallback onTap;

  const _BookingCard({required this.booking, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');

    Color statusColor;
    switch (booking.status) {
      case 'pending':   statusColor = Colors.orange; break;
      case 'approved':  statusColor = Colors.green;  break;
      case 'rejected':  statusColor = Colors.red;    break;
      case 'completed': statusColor = Colors.blue;   break;
      default:          statusColor = Colors.grey;
    }

    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 6, offset: const Offset(0, 2))],
        ),
        child: Column(
          children: [
            // Header status
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.08),
                borderRadius: const BorderRadius.vertical(top: Radius.circular(14)),
              ),
              child: Row(
                children: [
                  Text('Booking #${booking.id}',
                      style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
                  const Spacer(),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(booking.statusLabel,
                        style: TextStyle(
                            color: statusColor, fontWeight: FontWeight.bold, fontSize: 12)),
                  ),
                ],
              ),
            ),
            // Body
            Padding(
              padding: const EdgeInsets.all(14),
              child: Row(
                children: [
                  // Foto mobil
                  ClipRRect(
                    borderRadius: BorderRadius.circular(10),
                    child: booking.car?.photo != null
                        ? Image.network(
                            '${ApiConfig.storageUrl}/${booking.car!.photo}',
                            width: 80, height: 68, fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => _placeholder(),
                          )
                        : _placeholder(),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(booking.car?.name ?? '-',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                      const SizedBox(height: 4),
                      Text(
                        '${dateFormat.format(DateTime.parse(booking.startDate))} → '
                        '${dateFormat.format(DateTime.parse(booking.endDate))}',
                        style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                      ),
                      Text('${booking.totalDays} hari',
                          style: TextStyle(color: Colors.grey.shade500, fontSize: 12)),
                      const SizedBox(height: 6),
                      Text(currency.format(booking.totalPrice),
                          style: const TextStyle(
                              color: Color(0xFF2563EB),
                              fontWeight: FontWeight.bold, fontSize: 15)),
                    ]),
                  ),
                  const Icon(Icons.chevron_right, color: Colors.grey),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _placeholder() => Container(
    width: 80, height: 68, color: Colors.grey.shade200,
    child: const Icon(Icons.directions_car, color: Colors.grey),
  );
}