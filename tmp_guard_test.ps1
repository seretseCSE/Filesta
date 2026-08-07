$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://127.0.0.1:8321'
$sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession

function Get-Token($url) {
    $page = Invoke-WebRequest -Uri $url -WebSession $sess -UseBasicParsing
    return ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
}

function Post($url, $body) {
    try { Invoke-WebRequest -Uri $url -Method POST -WebSession $sess -UseBasicParsing -Body $body -MaximumRedirection 0 -ErrorAction Stop | Out-Null } catch { return $_.Exception.Response.StatusCode }
}

$token = Get-Token "$base/login"
Post "$base/login" @{ _token = $token; identity = '0922222222'; secret = '5678' }

$page = Invoke-WebRequest -Uri "$base/sales" -WebSession $sess -UseBasicParsing
$token = ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value

$code = Post "$base/sales" @{ _token = $token; item_id = 1; quantity = 2; payment_method_id = 1 }
"store while session open: $code (expect 302)"
$code = Post "$base/sales/3" @{ _token = $token; _method = 'PUT'; quantity = 1; payment_method_id = 1 }
"update while session open: $code (expect 302)"
$code = Post "$base/sales/3" @{ _token = $token; _method = 'DELETE' }
"delete while session open: $code (expect 302)"