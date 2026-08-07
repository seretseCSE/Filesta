$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://127.0.0.1:8321'
$sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession

$page = Invoke-WebRequest -Uri "$base/login" -WebSession $sess -UseBasicParsing
$token = ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value

$resp = Invoke-WebRequest -Uri "$base/login" -Method POST -WebSession $sess -UseBasicParsing -Body @{ _token = $token; identity = '0911111111'; secret = '1234' } -MaximumRedirection 0 -ErrorAction SilentlyContinue
"post status: $($resp.StatusCode) -> Location: $($resp.Headers.Location)"

$page2 = Invoke-WebRequest -Uri "$base/login" -WebSession $sess -UseBasicParsing
$token2 = ([regex]::Match($page2.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
"new token after failed attempt: $($token2.Substring(0,10))... (old was $($token.Substring(0,10))...)"

$resp2 = Invoke-WebRequest -Uri "$base/login" -Method POST -WebSession $sess -UseBasicParsing -Body @{ _token = $token2; identity = 'admin@filseta.test'; secret = 'secret123' } -MaximumRedirection 0 -ErrorAction SilentlyContinue
"admin post: $($resp2.StatusCode) -> Location: $($resp2.Headers.Location)"
