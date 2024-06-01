using MongoDB.Driver;
using Users.API.Models;

namespace Users.API.Services;

public class UserService(UserContext context)
{
  public async Task<List<User>> GetAllUsersAsync() =>
    await context.Users.Find(_ => true).ToListAsync();

  public async Task<User?> GetUserByEmailAsync(string email) =>
    await context.Users.Find(user => user.Email == email).FirstOrDefaultAsync();

  public async Task<User> CreateUserAsync(User user)
  {
    await context.Users.InsertOneAsync(user);
    return user;
  }

  public async Task UpdateUserAsync(string id, User user) =>
    await context.Users.ReplaceOneAsync(user => user.Id == id, user);

  public async Task DeleteUserAsync(string id) =>
    await context.Users.DeleteOneAsync(user => user.Id == id);
}
