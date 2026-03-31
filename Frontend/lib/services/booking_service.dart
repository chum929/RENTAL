import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/booking_model.dart';
import 'auth_service.dart';

class BookingService {
  // USER: Buat booking baru
  static Future<Map<String, dynamic>> createBooking({
    required int carId,
    required String startDate,
    required String endDate,
    String? notes,
  }) async {
    final headers = await AuthService.authHeaders();
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/bookings'),
      headers: headers,
      body: jsonEncode({
        'car_id':     carId,
        'start_date': startDate,
        'end_date':   endDate,
        if (notes != null && notes.isNotEmpty) 'notes': notes,
      }),
    );
    final data = jsonDecode(response.body);
    return {'success': response.statusCode == 201, ...data};
  }

  // USER: Ambil semua booking milik user
  static Future<List<BookingModel>> getMyBookings() async {
    final headers = await AuthService.authHeaders();
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/bookings'),
      headers: headers,
    );
    if (response.statusCode == 200) {
      final List list = jsonDecode(response.body);
      return list.map((e) => BookingModel.fromJson(e)).toList();
    }
    return [];
  }

  // USER: Detail booking
  static Future<BookingModel?> getBookingDetail(int id) async {
    final headers = await AuthService.authHeaders();
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/bookings/$id'),
      headers: headers,
    );
    if (response.statusCode == 200) {
      return BookingModel.fromJson(jsonDecode(response.body));
    }
    return null;
  }

  // USER: Batalkan booking
  static Future<bool> cancelBooking(int id) async {
    final headers = await AuthService.authHeaders();
    final response = await http.delete(
      Uri.parse('${ApiConfig.baseUrl}/bookings/$id'),
      headers: headers,
    );
    return response.statusCode == 200;
  }

  // OWNER: Ambil semua booking masuk
  static Future<List<BookingModel>> getOwnerBookings() async {
    final headers = await AuthService.authHeaders();
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/owner/bookings'),
      headers: headers,
    );
    if (response.statusCode == 200) {
      final List list = jsonDecode(response.body);
      return list.map((e) => BookingModel.fromJson(e)).toList();
    }
    return [];
  }

  // OWNER: Terima booking
  static Future<bool> approveBooking(int id) async {
    final headers = await AuthService.authHeaders();
    final response = await http.put(
      Uri.parse('${ApiConfig.baseUrl}/owner/bookings/$id/approve'),
      headers: headers,
    );
    return response.statusCode == 200;
  }

  // OWNER: Tolak booking
  static Future<bool> rejectBooking(int id) async {
    final headers = await AuthService.authHeaders();
    final response = await http.put(
      Uri.parse('${ApiConfig.baseUrl}/owner/bookings/$id/reject'),
      headers: headers,
    );
    return response.statusCode == 200;
  }
}