class BannerModel {
  final int id;
  final String? title;
  final String? description;
  final String imageUrl;
  final String? link;
  final int orderPosition;

  BannerModel({
    required this.id,
    this.title,
    this.description,
    required this.imageUrl,
    this.link,
    required this.orderPosition,
  });

  factory BannerModel.fromJson(Map<String, dynamic> json) {
    return BannerModel(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      imageUrl: json['image_url'],
      link: json['link'],
      orderPosition: json['order_position'] ?? 0,
    );
  }
}