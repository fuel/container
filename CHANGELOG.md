# Changelog

## 2.0.0
- added RuntimeValueArgument, to allow passing constructor arguments at runtime
- added getWith() and getNewWith() to pass runtime values to RuntimeValueArguments

### Fork
- This is a fork of https://github.com/thephpleague/container, version 4.2.0.

We forked it because encapsulating is too complex and has too much of a performance impact to be implemented in a development framework, and we needed to add specific Fuel support.
