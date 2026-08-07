$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://127.0.0.1:8321'
$sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession

$page = Invoke-WebRequest -Uri "$base/login" -WebSession $sess -UseBasicParsing
$token = ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value

$resp = Invoke-WebRequest -Uri "$base/login" -Method POST -WebSession $sess -UseBasicParsing -Body @{ _token = $token; identity = '0911111111'; secret = '1234' } -MaximumRedirection 0 -ErrorAction SilentlyContinue
"post status: $($resp.StatusCode)"

$after = Invoke-WebRequest -Uri "$base/login" -WebSession $sess -UseBasicParsing
if ($after.Content -match 'credentials do not match') { "ERROR: credentials do not match" }
elseif ($after.Content -match 'red-50') { "ERROR box present: " + ([regex]::Match($after.Content, '<div class="mt-4 rounded-xl bg-red-50[^>]*>(.*?)</div>', 'Singleline')).Groups[1].Value.Trim() }
else { "NO error box on login page" }

$check = Invoke-WebRequest -Uri "$base/admin" -WebSession $sess -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
"GET /admin: $($check.StatusCode) -> $($check.Headers.Location)"
