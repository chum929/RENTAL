import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/car_model.dart';
import '../config/api_config.dart';

class CarCard extends StatelessWidget {
  final CarModel car;
  final VoidCallback onTap;

  const CarCard({super.key, required this.car, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 8, offset: const Offset(0, 2))],
        ),
        child: Row(
          children: [
            // Foto mobil
            ClipRRect(
              borderRadius: const BorderRadius.horizontal(left: Radius.circular(16)),
              child: car.photo != null
                  ? Image.network(
                      '${ApiConfig.storageUrl}/${car.photo}',
                      width: 110, height: 90,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => _placeholder(),
                    )
                  : _placeholder(),
            ),
            // Info mobil
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(car.name,
                        style: const TextStyle(
                            fontWeight: FontWeight.bold, fontSize: 15)),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: Colors.blue.shade50,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(car.type,
                              style: TextStyle(fontSize: 10, color: Colors.blue.shade700)),
                        ),
                        const SizedBox(width: 6),
                        Icon(Icons.people_outline, size: 12, color: Colors.grey.shade600),
                        Text(' ${car.seats} kursi',
                            style: TextStyle(fontSize: 11, color: Colors.grey.shade600)),
                      ],
                    ),
                    const SizedBox(height: 6),
                    if (car.rentalProvider != null)
                      Row(children: [
                        Icon(Icons.location_on_outlined, size: 12, color: Colors.grey.shade500),
                        Text(' ${car.rentalProvider!.cityName ?? ''}',
                            style: TextStyle(fontSize: 11, color: Colors.grey.shade500)),
                      ]),
                    const SizedBox(height: 6),
                    Text(
                      '${currency.format(car.pricePerDay)}/hari',
                      style: const TextStyle(
                          color: Color(0xFF2563EB),
                          fontWeight: FontWeight.bold,
                          fontSize: 14),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _placeholder() => Container(
    width: 110, height: 90,
    color: Colors.grey.shade200,
    child: const Icon(Icons.directions_car, color: Colors.grey, size: 40),
  );
}