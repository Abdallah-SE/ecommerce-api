# Custom Exception System

This module provides a standardized exception handling system for the Laravel application with consistent error codes, status codes, and context information.

## Features

- ✅ **Consistent Error Format**: All exceptions return standardized JSON responses
- ✅ **Flexible Factory Pattern**: Easy creation of different exception types
- ✅ **Context Support**: Additional debugging information when needed
- ✅ **PSR-12 Compliant**: Follows PHP coding standards
- ✅ **Comprehensive Documentation**: Full PHPDoc comments
- ✅ **Laravel Integration**: Works seamlessly with Laravel's exception handling
- ✅ **Clean Error Messages**: No internal file paths or stack traces exposed to users
- ✅ **Security Focused**: Safe for production environments

## Clean Error Messages

The system provides clean, user-friendly error messages without exposing internal details:

### ✅ **Clean Response (Production)**
```json
{
    "status": false,
    "message": "Admin with ID 7 not found.",
    "code": "admin.not_found",
    "data": null
}
```

### ❌ **What We Avoid (Old Laravel Default)**
```json
{
    "message": "Admin with ID 7 not found.",
    "exception": "Modules\\Core\\Exceptions\\ApiException",
    "file": "/home/abdallah/work/3.projects/business/ecommerce-api/Modules/Core/Exceptions/ExceptionFactory.php",
    "line": 36,
    "trace": [...]
}
```

## Usage Examples

### Basic Exception Creation

```php
use Modules\Core\Exceptions\ExceptionFactory;

// Create a not found exception
$exception = ExceptionFactory::notFound('User', 123);
// Status: 404, Code: 'user.not_found', Message: 'User with ID 123 not found.'

// Create a validation exception
$exception = ExceptionFactory::validation([
    'email' => 'The email field is required.',
    'password' => 'The password must be at least 8 characters.'
]);
// Status: 422, Code: 'validation.failed'

// Create an unauthorized exception
$exception = ExceptionFactory::unauthorized('Invalid credentials');
// Status: 401, Code: 'auth.unauthorized'
```

### Available Factory Methods

| Method | Status Code | Error Code | Description |
|--------|-------------|------------|-------------|
| `notFound($entity, $id)` | 404 | `{entity}.not_found` | Entity not found |
| `validation($errors)` | 422 | `validation.failed` | Validation errors |
| `unauthorized($message)` | 401 | `auth.unauthorized` | Authentication required |
| `forbidden($message)` | 403 | `auth.forbidden` | Access denied |
| `badRequest($message)` | 400 | `request.bad` | Invalid request |
| `conflict($message)` | 409 | `request.conflict` | Resource conflict |
| `serverError($message)` | 500 | `server.error` | Internal server error |
| `serviceUnavailable($message)` | 503 | `service.unavailable` | Service unavailable |
| `custom($message, $code, $status)` | Custom | Custom | Custom exception |

### Direct ApiException Usage

```php
use Modules\Core\Exceptions\ApiException;

// Create with all parameters
$exception = new ApiException(
    'Custom error message',
    'custom.error_code',
    400,
    ['additional' => 'context']
);

// Access properties
$statusCode = $exception->getStatusCode(); // 400
$errorCode = $exception->getErrorCode();   // 'custom.error_code'
$context = $exception->getContext();       // ['additional' => 'context']

// Modify context
$exception->setContext(['new' => 'context']);
$exception->addContext(['additional' => 'data']);
```

### In Controllers

```php
use Modules\Core\Exceptions\ExceptionFactory;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            throw ExceptionFactory::notFound('User', $id);
        }
        
        return response()->json($user);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        if ($validator->fails()) {
            throw ExceptionFactory::validation($validator->errors()->toArray());
        }
        
        // Create user...
    }
}
```

### Exception Handler Integration

The `ExceptionHandlerTrait` provides consistent JSON responses:

```json
{
    "status": false,
    "message": "User with ID 123 not found.",
    "code": "user.not_found",
    "data": null,
    "errors": {
        "field": "error message"
    }
}
```

## Security Features

### 🔒 **Production Safe**
- No file paths exposed to users
- No stack traces in responses
- Clean, user-friendly messages
- Proper logging for debugging

### 🔍 **Debug Mode Support**
When `APP_DEBUG=true` in development:
```json
{
    "status": false,
    "message": "Admin with ID 7 not found.",
    "code": "admin.not_found",
    "data": null,
    "debug": {
        "entity_id": 7,
        "additional_context": "value"
    }
}
```

### 📝 **Comprehensive Logging**
All exceptions are logged with full details for debugging:
```php
Log::error('Exception occurred: ' . $exception->getMessage(), [
    'exception' => get_class($exception),
    'file' => $exception->getFile(),
    'line' => $exception->getLine(),
    'trace' => $exception->getTraceAsString(),
]);
```

## Best Practices

1. **Use Factory Methods**: Prefer `ExceptionFactory` methods over direct instantiation
2. **Consistent Error Codes**: Use descriptive, hierarchical error codes
3. **Context for Debugging**: Add relevant context information for debugging
4. **Proper Status Codes**: Use appropriate HTTP status codes
5. **Descriptive Messages**: Provide clear, user-friendly error messages
6. **Security First**: Never expose internal details to users
7. **Log Everything**: Always log exceptions for debugging

## File Structure

```
Modules/Core/Exceptions/
├── ApiException.php              # Base exception class
├── ExceptionFactory.php          # Factory for creating exceptions
├── README.md                     # This documentation
└── Traits/
    └── ExceptionHandlerTrait.php # Trait for handling exceptions
```

## Migration from Specific Exception Classes

The system now uses a generic approach instead of specific exception classes:

**Before (removed):**
```php
throw new AdminNotFoundException();
throw new UnauthorizedException();
throw new CustomValidationException($errors);
```

**After (current):**
```php
throw ExceptionFactory::notFound('Admin');
throw ExceptionFactory::unauthorized();
throw ExceptionFactory::validation($errors);
```

This approach reduces code duplication and provides more flexibility.

## React.js Integration

The clean error messages are perfect for React.js applications:

```javascript
// React.js error handling
const handleError = (error) => {
  if (error.response) {
    const { status, data } = error.response;
    
    switch (data.code) {
      case 'admin.not_found':
        setMessage('Admin not found');
        break;
      case 'validation.failed':
        setErrors(data.errors);
        break;
      case 'auth.unauthorized':
        redirectToLogin();
        break;
      default:
        setMessage(data.message);
    }
  }
};
```

The consistent JSON structure makes it easy to handle errors in your React frontend! 🚀 