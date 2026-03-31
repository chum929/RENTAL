class BookingModel {
  final int id;
  final int userId;
  final int carId;
  final String startDate;
  final String endDate;
  final int totalDays;
  final double totalPrice;
  final String status;
  final String? notes;
  final CarInfo? car;
  final UserInfo? user;

  BookingModel({
    required this.id,
    required this.userId,
    required this.carId,
    required this.startDate,
    required this.endDate,
    required this.totalDays,
    required this.totalPrice,
    required this.status,
    this.notes,
    this.car,
    this.user,
  });

  factory BookingModel.fromJson(Map<String, dynamic> json) {
    return BookingModel(
      id:         json['id'],
      userId:     json['user_id'],
      carId:      json['car_id'],
      startDate:  json['start_date'],
      endDate:    json['end_date'],
      totalDays:  json['total_days'],
      totalPrice: double.parse(json['total_price'].toString()),
      status:     json['status'],
      notes:      json['notes'],
      car:  json['car']  != null ? CarInfo.fromJson(json['car'])   : null,
      user: json['user'] != null ? UserInfo.fromJson(json['user']) : null,
    );
  }

  // Helper warna status
  bool get isPending   => status == 'pending';
  bool get isApproved  => status == 'approved';
  bool get isRejected  => status == 'rejected';
  bool get isCompleted => status == 'completed';
  bool get isCancelled => status == 'cancelled';

  String get statusLabel => switch (status) {
    'pending'   => 'Menunggu',
    'approved'  => 'Disetujui',
    'rejected'  => 'Ditolak',
    'ongoing'   => 'Berlangsung',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    _           => status,
  };
}

class CarInfo {
  final int id;
  final String name;
  final String type;
  final String? photo;
  final double pricePerDay;
  final ProviderInfo? rentalProvider;

  CarInfo({
    required this.id,
    required this.name,
    required this.type,
    this.photo,
    required this.pricePerDay,
    this.rentalProvider,
  });

  factory CarInfo.fromJson(Map<String, dynamic> json) {
    return CarInfo(
      id:           json['id'],
      name:         json['name'],
      type:         json['type'],
      photo:        json['photo'],
      pricePerDay:  double.parse(json['price_per_day'].toString()),
      rentalProvider: json['rental_provider'] != null
          ? ProviderInfo.fromJson(json['rental_provider'])
          : null,
    );
  }
}

class ProviderInfo {
  final int id;
  final String businessName;

  ProviderInfo({required this.id, required this.businessName});

  factory ProviderInfo.fromJson(Map<String, dynamic> json) {
    return ProviderInfo(
      id:           json['id'],
      businessName: json['business_name'],
    );
  }
}

class UserInfo {
  final int id;
  final String name;
  final String email;
  final String? phone;

  UserInfo({required this.id, required this.name, required this.email, this.phone});

  factory UserInfo.fromJson(Map<String, dynamic> json) {
    return UserInfo(
      id:    json['id'],
      name:  json['name'],
      email: json['email'],
      phone: json['phone'],
    );
  }
}