class UserModel {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? photo;
  final bool isActive;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.photo,
    required this.isActive,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id:       json['id'],
      name:     json['name'],
      email:    json['email'],
      phone:    json['phone'],
      role:     json['role'],
      photo:    json['photo'],
      isActive: json['is_active'] == 1 || json['is_active'] == true,
    );
  }

  bool get isOwner => role == 'owner';
  bool get isUser  => role == 'user';
}