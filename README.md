# Template-Mailer

[![Downloads this Month](https://img.shields.io/packagist/dm/leuffen/template-mailer.svg)](https://packagist.org/packages/leuffen/template-mailer)
[<img src="https://travis-ci.org/dermatthes/template-mailer.svg">](https://travis-ci.org/dermatthes/template-mailer)
[![Latest Stable Version](https://poser.pugx.org/leuffen/template-mailer/v/stable)](https://github.com/dermatthes/template-mailer/releases)
[![Supports PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)


This project combines an powerfull but easy-to-use OOP interface to Multipart-Mime-Mails in conjunction
 with a easy to learn template-language for defining complete multipart-messages within one string. (Including
 subject, from, other headers, all kinds of message-parts).

## Install

Install using composer:

```
composer require leuffen/template-mailer
```

## Basic Example
> See directory `/doc/template/tpl/` for a bunch of working examples. You can life-test the examples with 
> `/doc/template/SendTestMail.php` against your MailClient / MTA.

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
<mailPart contentType="text/html" CHARSET="UTF-8">
... do the same stuff in HTML
</mailPart>
```
*Notice: To display the html-content by default it's important, to put the text/html content to the end of the mail*

Load the template and send the mail:

```php
$parser = new MailTemplateParser();
$parser->loadTemplate($template);
$parser->send ($orderData);
```
That's it


### Adding MailParts to a template

Use the `apply()` method to not just send the mail but return the `MailBody`:

```php
$parser = new MailTemplateParser();
$parser->loadTemplate ($template);
$mail = $parser->apply($orderData);
```

Add an attachment using the `FileAttachment` helper class:

```php
$mail->addPart(new FileAttachment("path/to/image.jpg"));
```

or the manual way:

```php
$mail->addPart(
    $_part = new MailBody(
        chunk_split(
            base64_encode(
                file_get_contents($fileName)
                ),
                75
            ),    
        mime_content_type($fileName),
        "UTF-8",
        "base64"
    )
);
$_part->setContentDispositionFileName(basename($fileName));
```

send the mail:

```php
$mail->send();
```

That's it.

## Programmatic multipart example
You can use template-mailer to send multipart-mail without any templating directly from your code:


```php
$mailBody = new MailBody ("to@address.de", "Some subject", "from@me.de");
$mailBody->addPart (new MailPart ("Some Text Content", "text/plain"));
$mailBody->addPart (new MailPart ("<h1>Some Html Part</h1>", "text/html"));
$mailBody->send();
```


## Using the Template Engine

For standard purpose you should use the MailTemplateParser to generate your Mail. But template-mailer can
also be used like an OOP Mail Frontend. You'll find mor Information about that topic in the next section.

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

Template consist of two sections. The *header-section*...

```
To: Some User <some@user.com>
Subject: Some fancy mail subject
```
... followed by __one empty line__, and the *body-section*...
```
<mailPart contentType="text/html">
  ..content goes here..
</mailPart>

..more mail-parts..
```
... where you define each part of your mail between `<mailPart>` and `</mailPart>`.

You don't have to take care about boundaries, additional headers nor correct escaping.

When defining mailPart you can use the attributes defined below to fit your needs:

| Attribute              | Description                                 | Allowed values |
|------------------------|---------------------------------------------|----------------|
| contentType            |                                             | text/plain, text/html, application/pdf, ... |
| contentDisposition     |                                             | attachment, inline |
| contentTransferEncoding| Default: 8Bit                               | 8Bit, quoted-printable, base64 |
| charset                | Default: UTF-8                              | utf-8, iso-8895-1, ...|
| fileName               | Used with contentDispositon="attachment": The filename to display in attachements |  |
| token                  |           | |
| id                     | Used to reference attached files. If you name it `somefile` you can reference it `<img src="cid:somefile">` |  |
| skipEncoding           | If skipEncoding="no" is present, Text-Template will do no own encoding to the contents | YES, NO |


### The MailBody Content-Type

One word to the content-type of the container-mail:

* __`multipart/mixed`__: Default, if more than one mailPart isset
    * All Parts are shown in order of occurence
    * Attachments possible
    
* __`multipart/alternative`__: Use this if you want to display only one part (HTML or TEXt) with inline Attachements
    * Last part has highest precedence
    * Attachments will not be displayed

* __`multipart/related`__: Use this if you want to embed attachements into the mail
    * First part has highest precedence
    * Inline-Attachments will not be displayed if they are displayed inside html-code.


By default, Template-Mailer will set the content-type in de header of the container-mail to `multipart/mixed`, which
allows multiple attachments.

But it will display all attachments.

You can set it to use `multipart/alternative` by adding the Header

```
..in Header..
Content-Type: multipart/alternative
```

### Debugging the context

To see the structure of the data passed to the template just add

```
{= __CONTEXT__ | raw } 
```

to your template. It will output the structure of the context.



### Accessing the TextTemplate Parser

To access and configure the TextTemplate Parser use the `MailTemplateParser::getTextTemplateParser()` method.

Example:

```
$mailTemplateParser->getTextTemplateParser()->addFilter("price", function ($input) { return number_format($input) });
```

### Changing the Deliveryagent to an non local SMTP Server

To change the DeliveryAgent, it must be set in the MailKernel, via the static function SetMailDeliveryAgent
The SmtpDeliveryAgent uses the class PHPMailer to send an SMTP Message to the chosen Server

Example:

```
$mailDeliveryAgent = new SmtpDeliveryAgent("localhost");
MailKernel::SetMailDeliveryAgent($mailDeliveryAgent);
```

## About
Template-Mailer was written by Matthias Leuffen <http://leuffen.de>

