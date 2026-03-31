import 'package:flutter/foundation.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';

class AuthProvider extends ChangeNotifier {
  UserModel? _user;
  bool _isLoading = false;

  UserModel? get user    => _user;
  bool get isLoading     => _isLoading;
  bool get isLoggedIn    => _user != null;
  bool get isOwner       => _user?.isOwner ?? false;

  // Cek apakah sudah login saat app dibuka
  Future<void> checkAuth() async {
    final token = await AuthService.getToken();
    if (token != null) {
      _user = await AuthService.getProfile();
      notifyListeners();
    }
  }

  Future<Map<String, dynamic>> login(String email, String password) async {
    _isLoading = true;
    notifyListeners();

    final result = await AuthService.login(email: email, password: password);

    if (result['success']) {
      _user = UserModel.fromJson(result['user']);
    }
    _isLoading = false;
    notifyListeners();
    return result;
  }

  Future<void> logout() async {
    await AuthService.logout();
    _user = null;
    notifyListeners();
  }
}