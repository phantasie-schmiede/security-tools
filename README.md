# PSBits Security Tools

- [What does it do?](#what-does-it-do)
- [How it works](#how-it-works)
- [Checksum details](#checksum-details)
- [Tokens](#tokens)
- [Auditing corrupted data](#auditing-corrupted-data)

### What does it do?

This package is an extension for TYPO3 CMS that checks the integrity of your domain model records by calculating a
checksum based on a canonical representation of the object and the encryption key of your TYPO3 installation. It
detects unwanted manipulation of your domain models by throwing an exception if the checksum does not match when trying
to access the model. This is especially useful for models with sensitive data, where you want to ensure that the data
has not been tampered with.

It also provides a base for single use tokens (e.g. for password resets) where only a hashed validator is stored in the
database, and bulk operations to clean up expired tokens.

### How it works

Let your domain models extend the class `PSBits\SecurityTools\Domain\Model\AbstractProtectedModel` to protect their data from
unwanted manipulation. Remember to call the parent constructor in your model's constructor after initializing the
properties, e.g. ObjectStorages! If your repository extends
`PSBits\SecurityTools\Domain\Repository\AbstractProtectedRepository`, the checksum will be calculated and set automatically
when adding or updating a record of this model. `add()` persists the checksum on its own, so a record added through the
repository is always consistent even if no further `persistAll()` is triggered.

### Checksum details

The checksum is an HMAC (SHA-256 by default) over a deterministic, canonical representation of the object, signed with
the encryption key of your TYPO3 installation. Compared to a plain serialization, this representation

- explicitly includes null values, so clearing a protected field is detectable,
- normalizes `DateTime` values to a fixed UTC format,
- represents related domain objects by their `ClassName:uid` instead of a raw, hydration dependent serialization, and
- is independent of the property declaration order.

System metadata fields (`pid`, `tstamp`, `crdate`) are excluded, so legitimate operations like moving a record to
another page do not invalidate the checksum. The record identity (`uid`) is included, so a record cannot be cloned with
a still valid checksum.

Keep in mind that the checksum is bound to the canonical representation of the object and the encryption key of your
TYPO3 installation. If you change the encryption key, add or remove properties, edit the record directly in the
database or update the model in your code without updating the checksum, the checksum will not match anymore and the
model will throw an exception when trying to access it! Provide UpgradeWizards when updating the database schema or the
model to recalculate the checksum for all existing records.

### Tokens

Let your token models extend `PSBits\SecurityTools\Domain\Model\AbstractToken`. A random token is generated, and only a
hashed validator (plus a selector and an expiration date) is persisted, so a leaked database does not expose the raw
token. Use `validate()` to check a presented token; it returns `false` once the token has expired.

For single use tokens, remove the token right after a successful validation. Let your token repository extend
`PSBits\SecurityTools\Domain\Repository\AbstractTokenRepository` to get

- `removeBySelector(string $selector)` to invalidate a token after it was used, and
- `removeExpired(): int` to clean up all expired tokens (e.g. from a scheduled task).

### Auditing corrupted data

Whenever the checksum validation fails, the event listener
`PSBits\SecurityTools\EventListener\ValidateChecksumAfterObjectThawed` logs the incident (model class and record uid) before
re-throwing the `CorruptedDataException`. This makes tampering visible in the TYPO3 log.
