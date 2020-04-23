class NoteSheet{


    constructor(labID, noteSheetID){
        this.labID = labID;
        this.noteSheetID = noteSheetID;
        this.notes = new Array();
        // Store the ID for each note in a seperate array with the indexes matching
        // NOT SURE HOW TO PASS THE WHOLE NOTE OBJEC OR THIS COULD BE AVOIDED
        this.notesIDs = new Array();
    }

    // note is a Note object
    // DO WE WANT TO ALSO STORE THE noteID HERE TOO?
    addNote(note, id){
        console.log("Adding a note to notesheet " + this.noteSheetID +" with text " + note);
        this.notes.push(note);
        this.notesIDs.push(id);
        //console.log(this.notes);
    }

    printNotes(){
        // -1 since the last array value is always undefined
        console.log("\nPrinting notesheet with ID " + this.noteSheetID);
        for (var i = 0; i < myNoteSheet.notes.length; i++){
            console.log(myNoteSheet.notes[i]);
        }
    }
} // end NoteSheet class

class Note{

    constructor(noteSheet, graderID, noteID, text){
        this.noteSheet = noteSheet;
        this.graderID = graderID;
        this.noteID = noteID;
        this.text = text;
        // Add the note to the NoteSheet
        noteSheet.addNote(text, noteID);

        // Not needed, just for verification purposes - will remove later
        //console.log(this.sheetID);
        //console.log(this.graderID);
        //console.log(this.noteID);
        //console.log(this.text);
    }

    getText(){
        return this.text;
    }

    getNoteID(){
        return this.noteID;
    }
} // end Note class

myNoteSheet = new NoteSheet(1000, 1111);

note1 = new Note(myNoteSheet, 1011, 1101, "This is the first new note");
note2 = new Note(myNoteSheet, 1010, 1110, "This is the second new note");
note3 = new Note(myNoteSheet, 1110, 1000, "This is the third new note");
note4 = new Note(myNoteSheet, 1101, 1001, "This is the fourth new note");

myNoteSheet.printNotes();
console.log("\nPrinting note1:");
console.log(note1);

// Get the noteSheetID for the NoteSheet that the Note belongs to
console.log("\nPrinting the noteSheetID that note1 belongs to:");
console.log(note1.noteSheet.noteSheetID);
