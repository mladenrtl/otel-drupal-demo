using Users.API.Models;
using Users.API.Services;

namespace Users.API;

public static class UserApi
{
  public static RouteGroupBuilder MapUserApi(this IEndpointRouteBuilder routes)
  {
    var group = routes.MapGroup("/users");

    group.MapGet("", async (UserService userService, ILogger<Program> logger) =>
      {
        var users = await userService.GetAllUsersAsync();
        return Results.Ok(users);
      });

    group.MapGet("/{email}", async (UserService userService, string email) =>
      {
        if (email == "boom!")
        {
          throw new Exception("Boom! this is a demo exception");
        }

        var user = await userService.GetUserByEmailAsync(email);
        return user is null ? Results.NotFound() : Results.Ok(user);
      });

    group.MapPost("", async (UserService userService, User user) =>
      {
        await userService.CreateUserAsync(user);
        return Results.Created($"/users/{user.Email}", user);
      });

    group.MapPut("/{id}", async (UserService userService, string id, User user) =>
      {
        var existingUser = await userService.GetUserByEmailAsync(user.Email);
        if (existingUser is null)
        {
          return Results.NotFound();
        }

        user = user with { Id = id };
        await userService.UpdateUserAsync(id, user);

        return Results.NoContent();
      });

    group.MapDelete("/{id}", async (UserService userService, string id) =>
      {
        var user = await userService.GetUserByEmailAsync(id);
        if (user is null)
        {
          return Results.NotFound();
        }

        await userService.DeleteUserAsync(id);
        return Results.NoContent();
      });

    return group;
  }
}
