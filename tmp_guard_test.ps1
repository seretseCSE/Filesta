$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://127.0.0.1:8321'
$sess = New-Object Microsoft.PowerShell.Commands.WebRequestSession

function Get-Token($url) {
    $page = Invoke-WebRequest -Uri $url -WebSession $sess -UseBasicParsing
    return ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value
}

function PostStatus($url, $body) {
    try { Invoke-WebRequest -Uri $url -Method POST -WebSession $sess -UseBasicParsing -Body $body -MaximumRedirection 0 -ErrorAction Stop | Out-Null; return 302 } catch { return [int]$_.Exception.Response.StatusCode }
}

$token = Get-Token "$base/login"
$loginResult = PostStatus "$base/login" @{ _token = $token; identity = '0922222222'; secret = '5678' }
"login: $loginResult (expect 302)"

$check = Invoke-WebRequest -Uri "$base/sales" -WebSession $sess -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
"after login, /sales: $($check.StatusCode) (expect 200)"

$page = Invoke-WebRequest -Uri "$base/sales" -WebSession $sess -UseBasicParsing
$token = ([regex]::Match($page.Content, 'name="_token" value="([^"]+)"')).Groups[1].Value

"store (session open):   " + (PostStatus "$base/sales" @{ _token = $token; item_id = 1; quantity = 2; payment_method_id = 1 }) + " (expect 302)"
"update (session open):  " + (PostStatus "$base/sales/3" @{ _token = $token; _method = 'PUT'; quantity = 1; payment_method_id = 1 }) + " (expect 302)"
"delete (session open):  " + (PostStatus "$base/sales/3" @{ _token = $token; _method = 'DELETE' }) + " (expect 302)"
