import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/car_model.dart';
import '../../services/car_service.dart';
import '../../config/api_config.dart';
import 'add_car_screen.dart';

class ManageCarsScreen extends StatefulWidget {
  const ManageCarsScreen({super.key});

  @override
  State<ManageCarsScreen> createState() => _ManageCarsScreenState();
}

class _ManageCarsScreenState extends State<ManageCarsScreen> {
  List<CarModel> _cars = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _isLoading = true);
    final cars = await CarService.getOwnerCars();
    setState(() { _cars = cars; _isLoading = false; });
  }

  Future<void> _delete(int carId, String name) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Hapus Mobil?'),
        content: Text('Hapus "$name"? Tindakan ini tidak bisa dibatalkan.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Hapus', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
    if (confirm == true) {
      await CarService.deleteCar(carId);
      _load();
    }
  }

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      appBar: AppBar(title: const Text('Kelola Mobil')),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(
          context, MaterialPageRoute(builder: (_) => const AddCarScreen()),
        ).then((_) => _load()),
        icon: const Icon(Icons.add),
        label: const Text('Tambah Mobil'),
        backgroundColor: const Color(0xFF2563EB),
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _cars.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('🚗', style: TextStyle(fontSize: 52)),
                      const SizedBox(height: 12),
                      const Text('Belum ada mobil terdaftar'),
                      const SizedBox(height: 8),
                      ElevatedButton(
                        onPressed: () => Navigator.push(
                          context, MaterialPageRoute(builder: (_) => const AddCarScreen()),
                        ).then((_) => _load()),
                        child: const Text('Tambah Mobil Pertama'),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.builder(
                    padding: const EdgeInsets.fromLTRB(14, 14, 14, 90),
                    itemCount: _cars.length,
                    itemBuilder: (_, i) {
                      final car = _cars[i];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          boxShadow: [BoxShadow(
                              color: Colors.black.withOpacity(0.05), blurRadius: 6)],
                        ),
                        child: ListTile(
                          contentPadding: const EdgeInsets.all(12),
                          leading: ClipRRect(
                            borderRadius: BorderRadius.circular(8),
                            child: car.photo != null
                                ? Image.network(
                                    '${ApiConfig.storageUrl}/${car.photo}',
                                    width: 70, height: 60, fit: BoxFit.cover,
                                    errorBuilder: (_, __, ___) => _placeholder(),
                                  )
                                : _placeholder(),
                          ),
                          title: Text(car.name,
                              style: const TextStyle(fontWeight: FontWeight.bold)),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('${car.type} · ${car.seats} kursi · ${car.year}',
                                  style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
                              Text(currency.format(car.pricePerDay) + '/hari',
                                  style: const TextStyle(
                                      color: Color(0xFF2563EB), fontWeight: FontWeight.bold)),
                            ],
                          ),
                          trailing: Column(
                            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                            children: [
                              // Toggle ketersediaan
                              GestureDetector(
                                onTap: () async {
                                  await CarService.toggleAvailability(car.id, car.isAvailable);
                                  _load();
                                },
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: car.isAvailable
                                        ? Colors.green.shade50 : Colors.red.shade50,
                                    borderRadius: BorderRadius.circular(20),
                                    border: Border.all(
                                      color: car.isAvailable
                                          ? Colors.green.shade300 : Colors.red.shade300,
                                    ),
                                  ),
                                  child: Text(
                                    car.isAvailable ? 'Tersedia' : 'Tidak',
                                    style: TextStyle(
                                      fontSize: 11,
                                      color: car.isAvailable
                                          ? Colors.green.shade700 : Colors.red.shade700,
                                    ),
                                  ),
                                ),
                              ),
                              GestureDetector(
                                onTap: () => _delete(car.id, car.name),
                                child: const Icon(Icons.delete_outline, color: Colors.red, size: 20),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
    );
  }

  Widget _placeholder() => Container(
    width: 70, height: 60, color: Colors.grey.shade200,
    child: const Icon(Icons.directions_car, color: Colors.grey),
  );
}