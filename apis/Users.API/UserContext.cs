using MongoDB.Driver;
using MongoDB.Driver.Core.Extensions.DiagnosticSources;
using Users.API.Models;

namespace Users.API;

public class UserContext
{
  private readonly IMongoDatabase _database;

  public UserContext(IConfiguration configuration)
  {
    var settings = MongoClientSettings.FromConnectionString(configuration.GetConnectionString("MongoDB"));
    var instrumentationOptions = new InstrumentationOptions { CaptureCommandText = true };
    settings.ClusterConfigurator = cb => cb.Subscribe(new DiagnosticsActivityEventSubscriber(instrumentationOptions));
    var client = new MongoClient(settings);
    _database = client.GetDatabase("UserDB");
  }

  public IMongoCollection<User> Users => _database.GetCollection<User>("Users");
}
