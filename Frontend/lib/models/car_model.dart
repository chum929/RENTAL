class CarModel {
  final int id;
  final int rentalProviderId;
  final String name;
  final String type;
  final String plateNumber;
  final int year;
  final int seats;
  final double pricePerDay;
  final String? description;
  final String? photo;
  final bool isAvailable;
  final RentalProviderInfo? rentalProvider;

  CarModel({
    required this.id,
    required this.rentalProviderId,
    required this.name,
    required this.type,
    required this.plateNumber,
    required this.year,
    required this.seats,
    required this.pricePerDay,
    this.description,
    this.photo,
    required this.isAvailable,
    this.rentalProvider,
  });

  factory CarModel.fromJson(Map<String, dynamic> json) {
    return CarModel(
      id:               json['id'],
      rentalProviderId: json['rental_provider_id'],
      name:             json['name'],
      type:             json['type'],
      plateNumber:      json['plate_number'],
      year:             json['year'],
      seats:            json['seats'],
      pricePerDay:      double.parse(json['price_per_day'].toString()),
      description:      json['description'],
      photo:            json['photo'],
      isAvailable:      json['is_available'] == 1 || json['is_available'] == true,
      rentalProvider: json['rental_provider'] != null
          ? RentalProviderInfo.fromJson(json['rental_provider'])
          : null,
    );
  }
}

class RentalProviderInfo {
  final int id;
  final String businessName;
  final String? cityName;

  RentalProviderInfo({
    required this.id,
    required this.businessName,
    this.cityName,
  });

  factory RentalProviderInfo.fromJson(Map<String, dynamic> json) {
    return RentalProviderInfo(
      id:           json['id'],
      businessName: json['business_name'],
      cityName:     json['city']?['name'],
    );
  }
}