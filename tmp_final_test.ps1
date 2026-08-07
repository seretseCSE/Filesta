$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://127.0.0.1:8321'
$sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession

function Post($url, $body) {
    try { Invoke-WebRequest -Uri $url -Method POST -WebSession $sess -UseBasicParsing -Body $body -MaximumRedirection 0 -ErrorAction Stop | Out-Null } catch { }
}

$page = Invoke-WebRequest -Uri "$base/login" -WebSession $sess -UseBasicParsing
$token = ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
Post "$base/login" @{ _token = $token; identity = '0911111111'; secret = '1234' }

$blocked = Invoke-WebRequest -Uri "$base/sales" -WebSession $sess -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
"salesman /sales: $($blocked.StatusCode) -> $($blocked.Headers.Location)"

$page = Invoke-WebRequest -Uri "$base/sales/blocked" -WebSession $sess -UseBasicParsing
"blocked page: $($page.StatusCode)"
"name (Salesman): $($page.Content -match '>Salesman<')"
"congrats: $($page.Content -match 'Great work today')"
"confetti: $($page.Content -match 'confetti-piece')"
"stats (ETB 250): $($page.Content -match 'ETB 250')"
"bounce: $($page.Content -match 'animate-bounce')"
"pop-in: $($page.Content -match 'pop-in')"
