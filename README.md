# Magento 2 QuoteCleaner #

Clean old quotes from Magento using console command

# Install instructions #

`composer require dominicwatts/quotecleaner`

`php bin/magento setup:upgrade`

# Usage instructions #

xigen:quote:cleaner [-l|--limit [LIMIT]] [--] <clean>`

`php bin/magento xigen:quote:cleaner clean`

Configuration options to limit impact of console script

![Screenshot](https://i.snag.gy/WKMAXQ.jpg)

Or alternatively allow cron task to run
