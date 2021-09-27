# P Ham P -- PHP Ham Processor

This is a set of utilities and libraries for handling ham radio related stuff. We'll see what comes out of it eventually. It may even be scrapped/rewritten from scratch, possibly in another language.

## Features

- Parse Cabrillo logs (only QSOs at the moment).
- Check logs for common mistakes -- typos, missed ancillary prefixes/suffixes...

## Supported contests and events
- CQ World-Wide DX Contest, SSB

## Usage

To test a log from the CQ WW DX contest:
```
php scripts/check_cabrillo.php CQWWDXChecker (path/to/a/Cabrillo/logfile)
```

## TODO:
- Extend the Cabrillo parser to support headers and validation of logs.
- Composer support (make it a proper library).
- Tests.
