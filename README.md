# ItunesConnect-Autoingestion-PHP
Use the Autoingestion tool to download sales reports on Itunes Connect.

## API Reference
### getData(ReportType, ReportSubType, DateType, ReportDate);

* ReportType: Sales or Newsstand
* ReportSubType: Summary, Detailed, or Opt-In
* DateType: Daily, Weekly, Monthly, Yearly
* ReportDate: YYYYMMDD (Daily or Weekly),YYYYMM (Monthly) or YYYY (Yearly)

## Example

$autoingestion = new ItunesAutoIngestion(MY_USERNAME, MY_PASSWORD, MY_VENDOR_ID); <br/>
$data = $autoingestion->getData(Sales, Summary, Daily, 20160228); <br/>
<br/>
$data contains the summary reports of sales for Feb. 28, 2016<br/>

## Where is the Vendor ID

On Itunes Connect website, click on the Sales and Trends section, and your vendor ID will be displayed in the upper-left corner next to your developer name.

## Reference

https://www.apple.com/itunesnews/docs/AppStoreReportingInstructions.pdf

