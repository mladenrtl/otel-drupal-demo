using OpenTelemetry.Exporter;
using OpenTelemetry.Logs;
using OpenTelemetry.Resources;
using OpenTelemetry.Trace;
using Users.API;
using Users.API.Services;
using ExportProcessorType = OpenTelemetry.ExportProcessorType;

var builder = WebApplication.CreateBuilder(args);

Action<ResourceBuilder> configureResource = r => r.AddService(
  serviceName: builder.Configuration.GetValue("ServiceName", defaultValue: "Users.API")!,
  serviceVersion: typeof(Program).Assembly.GetName().Version?.ToString() ?? "unknown",
  serviceInstanceId: Environment.MachineName);

builder.Services.AddOpenTelemetry()
  .ConfigureResource(configureResource)
  .WithTracing(b =>
  {
    b.AddAspNetCoreInstrumentation(o =>
      {
        o.EnrichWithHttpRequest = (activity, request) =>
        {
          foreach (var header in request.Headers)
          {
            activity.SetTag("Header." + header.Key, header.Value.ToString());
          }
        };
      })
      .AddSource("MongoDB.Driver.Core.Extensions.DiagnosticSources")
      .AddOtlpExporter(o =>
      {
        // For demo purposes, use the simple processor.
        o.ExportProcessorType = ExportProcessorType.Simple;
        o.Endpoint = new Uri(builder.Configuration.GetValue("Otlp:Endpoint", defaultValue: "http://localhost:4317")!);
      });
  });

// Clear default logging providers and configure OpenTelemetry Logging.
builder.Logging.ClearProviders();
builder.Logging.AddOpenTelemetry(options =>
{
  var resourceBuilder = ResourceBuilder.CreateDefault();
  configureResource(resourceBuilder);
  options.SetResourceBuilder(resourceBuilder);
  options.AddOtlpExporter(otlpOptions =>
  {
    // For demo purposes, use the simple processor.
    otlpOptions.ExportProcessorType = ExportProcessorType.Simple;
    otlpOptions.Protocol = OtlpExportProtocol.HttpProtobuf;
    otlpOptions.Endpoint =
      new Uri(builder.Configuration.GetValue("OtlpLogs:Endpoint", defaultValue: "http://localhost:3100/otlp/v1/logs")!);

    if (builder.Environment.IsDevelopment())
    {
      options.AddConsoleExporter();
    }
  });
});

// Add services to the container.
builder.Services.AddSingleton<UserContext>();
builder.Services.AddSingleton<UserService>();
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

var app = builder.Build();

if (app.Environment.IsDevelopment())
{
  app.UseDeveloperExceptionPage();
}

app.UseSwagger();
app.UseSwaggerUI();

app.UseHttpsRedirection();

app.MapUserApi();

app.Run();
