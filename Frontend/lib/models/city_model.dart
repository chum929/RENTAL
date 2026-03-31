class CityModel {
  final int id;
  final String name;
  final String? province;

  CityModel({required this.id, required this.name, this.province});

  factory CityModel.fromJson(Map<String, dynamic> json) {
    return CityModel(
      id:       json['id'],
      name:     json['name'],
      province: json['province'],
    );
  }
}