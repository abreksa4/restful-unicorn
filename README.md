# [Restful-Unicorn](https://github.com/abreksa4/Restful-Unicorn)
_A simple MVC-like helper for building RAD Restful apps with [Unicorn](https://github.com/abreksa4/Unicorn)_

[![Build Status](https://travis-ci.org/abreksa4/restful-unicorn.svg?branch=master)](https://travis-ci.org/abreksa4/Restful-Unicorn)
[![Stories in Ready](https://badge.waffle.io/abreksa4/Restful-Unicorn.png?label=ready&title=Ready)](http://waffle.io/abreksa4/Restful-Unicorn)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation
`composer require andrewbreksa/restful-unicorn`

## Summary
This helper for [Unicorn](https://github.com/abreksa4/Unicorn) allows for the easy creation of Restful controller classes with automagic route mapping. Simply extend `AbstractRestfulController` and pass an Application instance and an array of class names to `AbstractRestfulController::bootstrapControllers()` to get up and running.

## Notes
See the test code and content of `public` for a better idea of how to use this helper. Better documentation is to follow.