# Changelog

## [6.0.0] - 2025-10-21

### ✨ New Feature
- **[Validators](https://github.com/PerfilovStanislav/php-dto-packer/tree/master?tab=readme-ov-file#validators)** - Remove default values  
  Added attributes **FieldValidators** and **ArrayValidators**
- **[Dimension](https://github.com/PerfilovStanislav/php-dto-packer/tree/master?tab=readme-ov-file#validators)** - Remove default values  
  Added attributes **FieldValidators** and **ArrayValidators**

## [5.3.0] - 2025-09-11

### ✨ New Feature
- **[Method pack()](https://github.com/PerfilovStanislav/php-dto-packer/blob/master/example/Example.php#L55)** - Remove default values  
  This method intelligently reduces the payload size by removing values that match their default state.
  This ensures leaner data transfer without losing essential information, making your DTOs more efficient for serialization and transport.

## [5.2.0] - 2025-09-10

### ✨ New Feature
- **[Mutators](https://github.com/PerfilovStanislav/php-dto-packer/blob/master/example/UserDto.php#L22)** - Support mutators for DTO fields  
  You can change values before they are added to the DTO.

## [5.1.0] - 2025-09-09

### ✨ New Feature
- **[Aliases](https://github.com/PerfilovStanislav/php-dto-packer/blob/master/example/UserDto.php#L21)** - Support for aliases in DTO fields  
  Now you can define alternative names for fields during packing,  
  which makes integration with inconsistent APIs and external services much easier.
