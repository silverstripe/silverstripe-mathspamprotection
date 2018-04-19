# Math Spam Protection

## Maintainers

 * Will Rossiter (Nickname: willr, wrossiter)
  <will at fullscreen dot io>

## Introduction

This module provides a simple math protection mechanism for prevent spam on your 
forms. It will ask the user to complete an equation such as "three plus seven".

Note that while the challenge is written in natural language to make it a bit harder to parse for bots,
its by no means a comprehensive solution to avoiding spam.

Includes an EditableMathSpamField to integrate with the UserForms module. 

## Requirements

 * SilverStripe 3.1
 * Spam Protection
 
## Install Spam Protection Module

The Spam Protection Module (http://silverstripe.org/spam-protection-module) 
provides the basic interface for managing the spam protection so first you need 
to install that module.

If you're using composer..

```
composer require "silverstripe/spamprotection:dev-master"
composer require "silverstripe/mathspamprotection:dev-master"
```

Set the default spam protector in *mysite/_config/spamprotection.yml*

	---
	name: spamprotection
	---
    SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension:
	  default_spam_protector: SilverStripe\SpamProtection\Maths\MathSpamProtector
