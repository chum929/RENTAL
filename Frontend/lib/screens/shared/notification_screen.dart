import 'package:flutter/material.dart';
import '../../services/notification_service.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  List<NotificationItem> _items = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final items = await NotificationService.getNotifications();
    setState(() { _items = items; _isLoading = false; });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notifikasi')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _items.isEmpty
              ? const Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text('🔔', style: TextStyle(fontSize: 48)),
                      SizedBox(height: 12),
                      Text('Belum ada notifikasi',
                          style: TextStyle(color: Colors.grey)),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(14),
                    itemCount: _items.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 8),
                    itemBuilder: (_, i) {
                      final item = _items[i];
                      return Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: item.isRead ? Colors.white : const Color(0xFFEFF6FF),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: item.isRead
                                ? Colors.grey.shade200
                                : const Color(0xFFBFDBFE),
                          ),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              width: 40, height: 40,
                              decoration: BoxDecoration(
                                color: item.isRead
                                    ? Colors.grey.shade100
                                    : const Color(0xFFDBEAFE),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Center(
                                child: Text(
                                  item.title.contains('Diterima') ? '✅'
                                  : item.title.contains('Ditolak') ? '❌'
                                  : '🔔',
                                  style: const TextStyle(fontSize: 18),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(item.title,
                                      style: TextStyle(
                                          fontWeight: item.isRead
                                              ? FontWeight.normal
                                              : FontWeight.bold,
                                          fontSize: 14)),
                                  const SizedBox(height: 3),
                                  Text(item.message,
                                      style: TextStyle(
                                          color: Colors.grey.shade600, fontSize: 13)),
                                  const SizedBox(height: 4),
                                  Text(
                                    _timeAgo(item.createdAt),
                                    style: TextStyle(
                                        color: Colors.grey.shade400, fontSize: 11),
                                  ),
                                ],
                              ),
                            ),
                            if (!item.isRead)
                              Container(
                                width: 8, height: 8,
                                margin: const EdgeInsets.only(top: 4),
                                decoration: const BoxDecoration(
                                  color: Color(0xFF2563EB),
                                  shape: BoxShape.circle,
                                ),
                              ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }

  String _timeAgo(DateTime dt) {
    final diff = DateTime.now().difference(dt);
    if (diff.inMinutes < 1)  return 'Baru saja';
    if (diff.inHours < 1)    return '${diff.inMinutes} menit lalu';
    if (diff.inDays < 1)     return '${diff.inHours} jam lalu';
    return '${diff.inDays} hari lalu';
  }
}