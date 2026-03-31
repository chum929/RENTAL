import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import '../models/user_model.dart';

class AuthService {
  // Simpan token ke local storage
  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', token);
  }

  // Ambil token yang tersimpan
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  // Hapus token (logout)
  static Future<void> removeToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
  }

  // Header untuk request yang perlu auth
  static Future<Map<String, String>> authHeaders() async {
    final token = await getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  // REGISTER
  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    required String passwordConfirmation,
    required String role, // 'user' atau 'owner'
    String? businessName,  // tambah ini
    int? cityId,           // tambah ini
    String? address,       // tambah ini
  }) async {
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/register'),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({
        'name':                  name,
        'email':                 email,
        'phone':                 phone,
        'password':              password,
        'password_confirmation': passwordConfirmation,
        'role':                  role,
      }),
    );

    final data = jsonDecode(response.body);
    if (response.statusCode == 201) {
      await saveToken(data['token']);
    }
    return {'success': response.statusCode == 201, ...data};
  }

  // LOGIN
  static Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/login'),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    final data = jsonDecode(response.body);
    if (response.statusCode == 200) {
      await saveToken(data['token']);
    }
    return {'success': response.statusCode == 200, ...data};
  }

  // LOGOUT
  static Future<void> logout() async {
    final headers = await authHeaders();
    await http.post(Uri.parse('${ApiConfig.baseUrl}/logout'), headers: headers);
    await removeToken();
  }

  // AMBIL PROFIL
  static Future<UserModel?> getProfile() async {
    try {
      final headers = await authHeaders();
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/me'),
        headers: headers,
      );
      if (response.statusCode == 200) {
        return UserModel.fromJson(jsonDecode(response.body));
      }
    } catch (e) {
      return null;
    }
    return null;
  }
  
}