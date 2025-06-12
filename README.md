# PSBits Security

- [What does it do?](#what-does-it-do)
- [How it works](#how-it-works)

### What does it do?

This package is an extension for TYPO3 CMS that checks the integrity of your domain model records by calculating a
checksum based on the serialized object and the encryption key of your TYPO3 installation. It detects unwanted
manipulation of your domain models by throwing an exception if the checksum does not match when trying to access the
model. This is especially useful for models with sensitive data, where you want to ensure that the data has not been
tampered with.

### How it works

Let your domain models extend the class `PSBits\Security\Domain\Model\AbstractProtectedModel` to protect their data from
unwanted manipulation. Remember to call the parent constructor in your model's constructor after initializing the
properties, e.g. ObjectStorages! If your repository extends
`PSBits\Security\Domain\Repository\AbstractProtectedRepository`, the checksum will be calculated and set automatically
when adding or updating a record of this model.

Keep in mind that the checksum is calculated based on the serialized object and the encryption key of your TYPO3
installation. If you change the encryption key, add or remove properties, edit the record directly in the database or
update the model in your code without updating the checksum, the checksum will not match anymore and the model will
throw an exception when trying to access it! Provide UpgradeWizards when updating the database schema or the model to
recalculate the checksum for all existing records.
