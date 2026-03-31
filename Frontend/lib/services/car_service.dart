import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/car_model.dart';
import 'auth_service.dart';

class CarService {
  // Ambil daftar mobil dengan filter
  static Future<List<CarModel>> getCars({
    String? search,
    String? cityId,
    String? type,
    String? minPrice,
    String? maxPrice,
  }) async {
    final params = <String, String>{};
    if (search != null && search.isNotEmpty)    params['search']    = search;
    if (cityId != null && cityId.isNotEmpty)    params['city_id']   = cityId;
    if (type != null && type.isNotEmpty)        params['type']      = type;
    if (minPrice != null && minPrice.isNotEmpty)params['min_price'] = minPrice;
    if (maxPrice != null && maxPrice.isNotEmpty)params['max_price'] = maxPrice;

    final uri = Uri.parse('${ApiConfig.baseUrl}/cars').replace(queryParameters: params);
    final response = await http.get(uri, headers: {'Accept': 'application/json'});

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      final List list = data['data'] ?? data;
      return list.map((e) => CarModel.fromJson(e)).toList();
    }
    return [];
  }

  // Detail satu mobil
  static Future<CarModel?> getCarDetail(int id) async {
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/cars/$id'),
      headers: {'Accept': 'application/json'},
    );
    if (response.statusCode == 200) {
      return CarModel.fromJson(jsonDecode(response.body));
    }
    return null;
  }

  // Mobil milik owner yang login
  static Future<List<CarModel>> getOwnerCars() async {
    final headers = await AuthService.authHeaders();
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/owner/cars'),
      headers: headers,
    );
    if (response.statusCode == 200) {
      final List list = jsonDecode(response.body);
      return list.map((e) => CarModel.fromJson(e)).toList();
    }
    return [];
  }

  // Tambah mobil (owner)
  static Future<Map<String, dynamic>> addCar({
    required String name,
    required String type,
    required String plateNumber,
    required int year,
    required int seats,
    required double pricePerDay,
    String? description,
    String? photoPath,
  }) async {
    final token = await AuthService.getToken();
    final request = http.MultipartRequest(
      'POST',
      Uri.parse('${ApiConfig.baseUrl}/owner/cars'),
    );
    request.headers['Authorization'] = 'Bearer $token';
    request.headers['Accept']        = 'application/json';
    request.fields['name']           = name;
    request.fields['type']           = type;
    request.fields['plate_number']   = plateNumber;
    request.fields['year']           = year.toString();
    request.fields['seats']          = seats.toString();
    request.fields['price_per_day']  = pricePerDay.toString();
    if (description != null) request.fields['description'] = description;
    if (photoPath != null) {
      request.files.add(await http.MultipartFile.fromPath('photo', photoPath));
    }
    final streamed = await request.send();
    final body = await streamed.stream.bytesToString();
    final data = jsonDecode(body);
    return {'success': streamed.statusCode == 201, ...data};
  }

  // Toggle ketersediaan mobil
  static Future<bool> toggleAvailability(int carId, bool isAvailable) async {
    final headers = await AuthService.authHeaders();
    final response = await http.put(
      Uri.parse('${ApiConfig.baseUrl}/owner/cars/$carId'),
      headers: headers,
      body: jsonEncode({'is_available': !isAvailable}),
    );
    return response.statusCode == 200;
  }

  // Hapus mobil
  static Future<bool> deleteCar(int carId) async {
    final headers = await AuthService.authHeaders();
    final response = await http.delete(
      Uri.parse('${ApiConfig.baseUrl}/owner/cars/$carId'),
      headers: headers,
    );
    return response.statusCode == 200;
  }
}