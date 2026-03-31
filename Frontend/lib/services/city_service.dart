import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/city_model.dart';

class CityService {
  static Future<List<CityModel>> getCities() async {
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/cities'),
      headers: {'Accept': 'application/json'},
    );
    if (response.statusCode == 200) {
      final List list = jsonDecode(response.body);
      return list.map((e) => CityModel.fromJson(e)).toList();
    }
    return [];
  }
}