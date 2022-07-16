***

# cookiePluginAction





* Full name: `\yxorP\app\plugin\cookiePluginAction`
* Parent class: [`\yxorP\app\lib\http\wrapper`](../lib/http/wrapper.md)


## Constants

| Constant | Visibility | Type | Value |
|:---------|:-----------|:-----|:------|
|`COOKIE_PREFIX`|public| |&#039;pc&#039;|


## Methods


### beforeRequest



```php
public static beforeRequest(mixed $match): mixed
```



* This method is **static**.




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$match` | **mixed** |  |




***

### headersReceived



```php
public static headersReceived(mixed $response, mixed $set_cookie): void
```



* This method is **static**.




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$response` | **mixed** |  |
| `$set_cookie` | **mixed** |  |




***

### Received



```php
public static Received(mixed $line): void
```



* This method is **static**.




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$line` | **mixed** |  |




***

### parse_cookie



```php
private static parse_cookie(mixed $line, mixed $url): array
```



* This method is **static**.




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$line` | **mixed** |  |
| `$url` | **mixed** |  |




***

### onBeforeSend



```php
public onBeforeSend(): void
```











***

### onSent



```php
public onSent(): void
```











***


## Inherited methods


### subscribe



```php
final public subscribe(\yxorP\app\yP $dispatcher): void
```





* This method is **final**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dispatcher` | **\yxorP\app\yP** |  |




***

### onCheck



```php
public onCheck(): void
```











***

### onBuildCache



```php
public onBuildCache(): void
```











***

### onBuildContext



```php
public onBuildContext(): void
```











***

### onBuildIncludes



```php
public onBuildIncludes(): void
```











***

### onBuildHeaders



```php
public onBuildHeaders(): void
```











***

### onBuildRequest



```php
public onBuildRequest(): void
```











***

### onBeforeSend



```php
public onBeforeSend(): void
```











***

### onSend



```php
public onSend(): void
```











***

### onSent



```php
public onSent(): void
```











***

### onWrite



```php
public onWrite(): void
```











***

### onComplete



```php
public onComplete(): void
```











***

### onFinal



```php
public onFinal(): void
```











***

### onBuildException



```php
public onBuildException(\Exception $e): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$e` | **\Exception** |  |




***

### route



```php
private route(string $event_name): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event_name` | **string** |  |




***


***
