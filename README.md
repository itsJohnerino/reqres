# ReqResService Class Implementation #

## Design Choices
### Exception Handling
- Custom Exceptions: Introduced ServiceValidationException and ServiceNotAvailableException to encapsulate specific error scenarios, enabling callers to handle these exceptions distinctly and improving error readability.
- Error Handling: Specific handling for 4xx and 5xx errors to provide clear feedback regarding client and server errors, respectively. This decision aids in debugging and ensures that errors are actionable.

### Implementation Details

#### Packages
<b>Guzzle</b>: Guzzle provides a straightforward and intuitive interface for making HTTP requests

<b>PestPHP</b>: PestPHP is a modern testing framework built on PHPUnit with a focus on simplicity and readability.

#### Methods
<b>getUserById</b>: Fetches a user by their ID, demonstrating the ability to retrieve individual resources.

<b>getPaginatedUsers</b>: Illustrates handling of paginated data, allowing for scalable data access patterns.

<b>createUser</b>: Enables the creation of new users, showcasing the service's support for diverse API interactions.

#### Error Messaging
Detailed error messages for 4xx client errors were implemented to provide clear, actionable feedback for API consumers. This choice was motivated by the need to facilitate easier debugging and to enhance the developer experience when dealing with API errors.
