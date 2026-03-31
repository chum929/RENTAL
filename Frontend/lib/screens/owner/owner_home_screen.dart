import 'package:flutter/material.dart';
import 'manage_cars_screen.dart';
import 'owner_bookings_screen.dart';
import '../shared/profile_screen.dart';
import '../shared/notification_screen.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../services/booking_service.dart';
import '../../models/booking_model.dart';

class OwnerHomeScreen extends StatefulWidget {
  const OwnerHomeScreen({super.key});

  @override
  State<OwnerHomeScreen> createState() => _OwnerHomeScreenState();
}

class _OwnerHomeScreenState extends State<OwnerHomeScreen> {
  int _currentIndex = 0;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: const [
          _OwnerDashboard(),
          ManageCarsScreen(),
          OwnerBookingsScreen(),
          ProfileScreen(),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (i) => setState(() => _currentIndex = i),
        selectedItemColor: const Color(0xFF2563EB),
        unselectedItemColor: Colors.grey,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.dashboard_outlined), label: 'Dashboard'),
          BottomNavigationBarItem(icon: Icon(Icons.directions_car_outlined), label: 'Mobil'),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long_outlined), label: 'Booking'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
      ),
    );
  }
}

class _OwnerDashboard extends StatefulWidget {
  const _OwnerDashboard();

  @override
  State<_OwnerDashboard> createState() => __OwnerDashboardState();
}

class __OwnerDashboardState extends State<_OwnerDashboard> {
  List<BookingModel> _pendingBookings = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final bookings = await BookingService.getOwnerBookings();
    setState(() {
      _pendingBookings = bookings.where((b) => b.isPending).toList();
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Dashboard Owner', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            Text(user?.name ?? '', style: const TextStyle(fontSize: 12, color: Colors.white70)),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () => Navigator.push(
                context, MaterialPageRoute(builder: (_) => const NotificationScreen())),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _load,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Banner pending
              if (_pendingBookings.isNotEmpty)
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(14),
                  margin: const EdgeInsets.only(bottom: 16),
                  decoration: BoxDecoration(
                    color: Colors.orange.shade50,
                    border: Border.all(color: Colors.orange.shade200),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(children: [
                    const Text('⏳', style: TextStyle(fontSize: 24)),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        '${_pendingBookings.length} booking menunggu konfirmasimu!',
                        style: TextStyle(color: Colors.orange.shade800, fontWeight: FontWeight.w600),
                      ),
                    ),
                  ]),
                ),

              const Text('Booking Perlu Tindakan',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const SizedBox(height: 12),

              _isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : _pendingBookings.isEmpty
                      ? Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(24),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Column(children: [
                            Text('🎉', style: TextStyle(fontSize: 36)),
                            SizedBox(height: 8),
                            Text('Semua booking sudah ditangani!',
                                style: TextStyle(color: Colors.grey)),
                          ]),
                        )
                      : Column(
                          children: _pendingBookings.map((booking) =>
                            _PendingBookingCard(
                              booking: booking,
                              onAction: _load,
                            ),
                          ).toList(),
                        ),
            ],
          ),
        ),
      ),
    );
  }
}

class _PendingBookingCard extends StatelessWidget {
  final BookingModel booking;
  final VoidCallback onAction;

  const _PendingBookingCard({required this.booking, required this.onAction});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [BoxShadow(
            color: Colors.black.withOpacity(0.05), blurRadius: 6)],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            const CircleAvatar(
              backgroundColor: Color(0xFFEFF6FF),
              child: Text('👤', style: TextStyle(fontSize: 16)),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(booking.user?.name ?? '-',
                    style: const TextStyle(fontWeight: FontWeight.bold)),
                Text(booking.car?.name ?? '-',
                    style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
              ]),
            ),
          ]),
          const SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('${booking.startDate} → ${booking.endDate}',
                  style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
              Text(
                'Rp ${booking.totalPrice.toStringAsFixed(0)}',
                style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF2563EB)),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(
              child: OutlinedButton(
                onPressed: () async {
                  await BookingService.rejectBooking(booking.id);
                  onAction();
                },
                style: OutlinedButton.styleFrom(
                  foregroundColor: Colors.red,
                  side: const BorderSide(color: Colors.red),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                child: const Text('✗ Tolak'),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: ElevatedButton(
                onPressed: () async {
                  await BookingService.approveBooking(booking.id);
                  onAction();
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                child: const Text('✓ Terima'),
              ),
            ),
          ]),
        ],
      ),
    );
  }
}