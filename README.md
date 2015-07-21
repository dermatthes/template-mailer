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





## About
Template-Mailer was written by Matthias Leuffen <http://leuffen.de>

