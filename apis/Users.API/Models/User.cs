using MongoDB.Bson;
using MongoDB.Bson.Serialization.Attributes;

namespace Users.API.Models;

public record User
{
  [BsonId]
  [BsonRepresentation(BsonType.ObjectId)]
  public string Id { get; init; } = default!;

  public required string Name { get; init; }

  public required string Email { get; init; }

  public required int Age { get; init; }
}
