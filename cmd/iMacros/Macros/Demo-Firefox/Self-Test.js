/* This script runs all demo macros and displays the success or failure of each run */
/* Created by: iMacros Team, March 18th, 2008 */

var i, retcode;
var report;
var macrolist = new Array();

/* Standard Demo Macros  */
macrolist.push("Demo-Firefox/FillForm.iim");
macrolist.push("Demo-Firefox/Frame.iim");
macrolist.push("Demo-Firefox/Filter.iim");
macrolist.push("Demo-Firefox/Tabs.iim");
macrolist.push("Demo-Firefox/Javascript-Dialogs.iim");
macrolist.push("Demo-Firefox/SlideShow.iim");
macrolist.push("Demo-Firefox/TagPosition.iim");
/* Macros that save something and/or create logs  */
macrolist.push("Demo-Firefox/Download.iim");
macrolist.push("Demo-Firefox/SaveAs.iim");
macrolist.push("Demo-Firefox/SavePDF.iim");
/* Macros that create reports */
macrolist.push("Demo-Firefox/Stopwatch.iim");
/* Macros that extract information */
macrolist.push("Demo-Firefox/ExtractAndFill.iim");
macrolist.push("Demo-Firefox/Extract.iim");
macrolist.push("Demo-Firefox/ExtractURL.iim");
macrolist.push("Demo-Firefox/ExtractRelative.iim");



iimDisplay("Start Self Test");

report  =  "Self-Test Report\n\n";

for (i = 0; i < macrolist.length; i++) {
    iimDisplay("Step "+(i+1)+" of "+macrolist.length + "\nMacro: "+macrolist[i]);
    retcode = iimPlay(macrolist[i]);
    report += macrolist[i];
    if (retcode < 0) {
        report += ": "+iimGetLastError();
    } else {
        report += ": OK";
        /* display the FIRST extracted item in report*/
        s = iimGetLastExtract(1);
        if ( s != "" )  report += ", Extract: "+s;
    }
    report += "\n";
}
iimDisplay("Test complete");

alert ( report );


