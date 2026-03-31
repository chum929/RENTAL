import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import 'auth_service.dart';

class NotificationItem {
  final int id;
  final String title;
  final String message;
  final bool isRead;
  final DateTime createdAt;

  NotificationItem({
    required this.id,
    required this.title,
    required this.message,
    required this.isRead,
    required this.createdAt,
  });

  factory NotificationItem.fromJson(Map<String, dynamic> json) {
    return NotificationItem(
      id:        json['id'],
      title:     json['title'],
      message:   json['message'],
      isRead:    json['is_read'] == 1 || json['is_read'] == true,
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}

class NotificationService {
  static Future<List<NotificationItem>> getNotifications() async {
    final headers = await AuthService.authHeaders();
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/notifications'),
      headers: headers,
    );
    if (response.statusCode == 200) {
      final List list = jsonDecode(response.body);
      return list.map((e) => NotificationItem.fromJson(e)).toList();
    }
    return [];
  }

  static Future<void> markRead(int id) async {
    final headers = await AuthService.authHeaders();
    await http.put(
      Uri.parse('${ApiConfig.baseUrl}/notifications/$id/read'),
      headers: headers,
    );
  }
}