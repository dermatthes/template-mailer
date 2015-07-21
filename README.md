# Template-Mailer

This Project is still under development. Check again in a few days.

## Install

@todo

## Basic Example
Define a template using text-template syntax

```
To: {= user.email};<support@some-company.com>
From: support@some-company.com
Subject: Your order {= oder.id} on our site

<mailPart contentType="text/plain" encoding="UTF-8">
Hello {= user.name},

you just ordered following items:

{for curItem in order.items}
{=@index1}: {=curItem.title|cutright:20} {=curItem.quantity} x {=curItem.price|currency}
{/for}
</mailPart>
<mailPart contentType="text/html" encoding="UTF-8">
... do the same stuff in HTML
</mailPart>
```
*Notice: To display the html-content by default it's important, to put the text/html content to the end of the mail*

Load the template and send the mail:

```php
$parser = new TemplateMailParser();
$parser->loadTemplate($template);
$parser->send ($orderData);

```
That's it


## Using the Template Engine

For normal reasons you should use the MailTemplateParser to generate your Mail. But template-mailer can
also be used like an OOP Mail Frontend. You'll find mor Information about this topic in the next section.

### Security enhancements when using Templates

* __Auto Escaping__: TemplateParser will escape values according to your content-type. Values in
    text/html mailParts will be htmlspecialchars()'d

* __Mail injection__: Values in the header-section will be automaticly trimmed to 255 bytes and LineBreaks
    are converted to spaces

* __Transparent Encoding__: The templating-engine will set and encode any content in the best available
    transfer-encoding

* __UTF-8 by default__: Both, templates and values are expected in corret UTF-8 formatting. By default
    outgoing mails will be UTF-8 formated as well - unless you change the Charset: - Header. In this case
    template-mail will do tranparent conversion for you.

### Defining Templates

Template-Mail uses Text-Template (http://github.com/dermatthes/text-template) Syntax to define your
templates.


## About
Template-Mailer was written by Matthias Leuffen <http://leuffen.de>

