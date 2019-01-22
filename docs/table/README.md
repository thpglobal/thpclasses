# Table Class

This class is designed to take a 2-D array of data and display it. It can do very basic things or very complex things. Those are separated here to minimize confusion. 

## Basic Attributes

* Table::contents - a two-d array. $this->contents[$i][$j] is a cell in row $i column $j. contents[0] is a header row. It is public to your app.

## Basic Methods

* Table::query($select_query) - run the query, filling in the field names in row 0, and all the records starting at row 1.
* Table::show($href) - display the query, linking the values of column 0. EG if $href="edit?id=" and the value of contents[1][0] is 5, then you would see a hyperlink on the 5 that goes to "edit?id=5." A copy of contents is saved to $_SESSION["contents"] for handy next steps such as export.
* Table::header($array) assigns the array to contents[0].
* Table::row($array) assigns the array to contents[] (the next unassigned row).

## Mapping and Rowpans

* **Mapping:** You may have a number of sparse columns that you want to map against a specific column - like a database outer join. It is often much easier to let this class do the joins than to try and build a single massive and complex query.
* **Rowspans:** You may have multiple subvalues mapped to a column - for example, you may have indicators "Voter Participation" and "Meeting Participation" and within those you might disaggregate it into Women, Men, Total_Participants where you want Total to be computed by the class rather than in the database. 

## Advanced Attributes
* Table::rowspan - an integer value for how many columns should have their rows merged, say when there is diaggregated data.
* Table::backx - a mapping array to map values into rows. Often it is 

## Advanced Methods
