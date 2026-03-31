import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/car_model.dart';
import '../../services/car_service.dart';
import '../../config/api_config.dart';
import 'booking_screen.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class CarDetailScreen extends StatefulWidget {
  final int carId;
  const CarDetailScreen({super.key, required this.carId});

  @override
  State<CarDetailScreen> createState() => _CarDetailScreenState();
}

class _CarDetailScreenState extends State<CarDetailScreen> {
  CarModel? _car;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    final car = await CarService.getCarDetail(widget.carId);
    setState(() { _car = car; _isLoading = false; });
  }

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    final isLoggedIn = context.watch<AuthProvider>().isLoggedIn;
    final isUser     = context.watch<AuthProvider>().user?.isUser ?? false;

    return Scaffold(
      backgroundColor: Colors.grey.shade100,
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _car == null
              ? const Center(child: Text('Mobil tidak ditemukan'))
              : CustomScrollView(
                  slivers: [
                    // App Bar dengan foto
                    SliverAppBar(
                      expandedHeight: 260,
                      pinned: true,
                      flexibleSpace: FlexibleSpaceBar(
                        background: _car!.photo != null
                            ? Image.network(
                                '${ApiConfig.storageUrl}/${_car!.photo}',
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => _photoPlaceholder(),
                              )
                            : _photoPlaceholder(),
                      ),
                    ),

                    SliverToBoxAdapter(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Nama & badge
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Expanded(
                                  child: Text(_car!.name,
                                      style: const TextStyle(
                                          fontSize: 22, fontWeight: FontWeight.bold)),
                                ),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: _car!.isAvailable
                                        ? Colors.green.shade50
                                        : Colors.red.shade50,
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    _car!.isAvailable ? '✅ Tersedia' : '❌ Tidak Tersedia',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: _car!.isAvailable
                                          ? Colors.green.shade700
                                          : Colors.red.shade700,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 6),
                            Text('${_car!.type} · ${_car!.year} · ${_car!.seats} kursi',
                                style: TextStyle(color: Colors.grey.shade600)),
                            const SizedBox(height: 16),

                            // Spesifikasi
                            _sectionCard(
                              title: 'Spesifikasi',
                              child: Row(
                                children: [
                                  _specItem('🚘', 'Plat', _car!.plateNumber),
                                  _specItem('👥', 'Kursi', '${_car!.seats} org'),
                                  _specItem('📅', 'Tahun', '${_car!.year}'),
                                  _specItem('🚗', 'Tipe', _car!.type),
                                ],
                              ),
                            ),

                            // Deskripsi
                            if (_car!.description != null) ...[
                              const SizedBox(height: 12),
                              _sectionCard(
                                title: 'Deskripsi',
                                child: Text(_car!.description!,
                                    style: TextStyle(color: Colors.grey.shade700, height: 1.5)),
                              ),
                            ],

                            // Info penyedia
                            if (_car!.rentalProvider != null) ...[
                              const SizedBox(height: 12),
                              _sectionCard(
                                title: 'Penyedia Rental',
                                child: Row(children: [
                                  Container(
                                    width: 44, height: 44,
                                    decoration: BoxDecoration(
                                      color: Colors.blue.shade100,
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: const Center(child: Text('🏢', style: TextStyle(fontSize: 20))),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                      Text(_car!.rentalProvider!.businessName,
                                          style: const TextStyle(fontWeight: FontWeight.bold)),
                                      if (_car!.rentalProvider!.cityName != null)
                                        Text('📍 ${_car!.rentalProvider!.cityName}',
                                            style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
                                    ]),
                                  ),
                                ]),
                              ),
                            ],

                            const SizedBox(height: 100), // ruang untuk bottom bar
                          ],
                        ),
                      ),
                    ),
                  ],
                ),

      // Bottom bar harga + tombol pesan
      bottomNavigationBar: _car == null ? null : Container(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 10, offset: const Offset(0, -2))],
        ),
        child: Row(
          children: [
            Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Harga per hari', style: TextStyle(color: Colors.grey, fontSize: 12)),
                Text(currency.format(_car!.pricePerDay),
                    style: const TextStyle(
                        fontSize: 20, fontWeight: FontWeight.bold, color: Color(0xFF2563EB))),
              ],
            ),
            const SizedBox(width: 16),
            Expanded(
              child: ElevatedButton(
                onPressed: (!isLoggedIn || !isUser || !_car!.isAvailable) ? null : () {
                  Navigator.push(context, MaterialPageRoute(
                    builder: (_) => BookingScreen(car: _car!),
                  ));
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text(
                  !isLoggedIn ? 'Login untuk Memesan'
                  : !isUser   ? 'Khusus User Penyewa'
                  : !_car!.isAvailable ? 'Tidak Tersedia'
                  : 'Pesan Sekarang',
                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _photoPlaceholder() => Container(
    color: Colors.grey.shade200,
    child: const Center(child: Icon(Icons.directions_car, size: 80, color: Colors.grey)),
  );

  Widget _sectionCard({required String title, required Widget child}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 6, offset: const Offset(0, 1))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
          const SizedBox(height: 10),
          child,
        ],
      ),
    );
  }

  Widget _specItem(String icon, String label, String value) {
    return Expanded(
      child: Column(children: [
        Text(icon, style: const TextStyle(fontSize: 22)),
        const SizedBox(height: 4),
        Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        Text(label, style: TextStyle(color: Colors.grey.shade500, fontSize: 11)),
      ]),
    );
  }
}