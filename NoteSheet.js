class NoteSheet{

    constructor(labID, noteSheetID){
        if (labID == undefined){
            console.log("LabID is required to make a new notesheet");
        }
        else if (noteSheetID == undefined){
            console.log("NoteSheetID is required to make a new notesheet");
        }
        else{
            this.labID = labID;
            this.noteSheetID = noteSheetID;
            this.notes = new Array();
            // Store the ID for each note in a seperate array with the indexes matching the notes array
            // NOT SURE HOW TO PASS THE WHOLE NOTE OBJECT OR THIS COULD BE AVOIDED
            this.notesIDs = new Array();
            // Can use the notesIDs array to find the NoteID and then use that index to print of the notes text from the notes array
        }
    } // end Constructor

    // note is a Note object
    // DO WE WANT TO ALSO STORE THE noteID HERE TOO? If not, need to remove.
    addNote(note, id){
        if (note == undefined){
            console.log("A new note must contain text - the note paramater can not be empty");
        }
        else if (id == undefined){
            console.log("A new note must have an ID - id paramater can not be empty");
        }
        else{
            console.log("Adding a note to notesheet " + this.noteSheetID +" with text: " + note);
            this.notes.push(note);
            this.notesIDs.push(id);
        }
    } // end addNote method

    printNotes(){
        console.log("\nPrinting notesheet with ID " + this.noteSheetID);
        for (var i = 0; i < this.notes.length; i++){
            console.log("NoteID: " + this.notesIDs[i] + " with text: " + this.notes[i]);
        }
    }
} // end NoteSheet class

class Note{

    constructor(noteSheet, graderID, noteID, text){
        if (noteSheet == undefined || graderID == undefined || noteID == undefined || text === undefined){
            console.log("Error creating note. Missing parameters. Please check parameters and retry")
        }
        else{
            this.noteSheet = noteSheet;
            this.graderID = graderID;
            this.noteID = noteID;
            this.text = text;
            // Add the note to the NoteSheet
            noteSheet.addNote(text, noteID);
        }
    } // end Constructor

    // Not sure we will need these getters, previously used but changed code, so unused now. Leaving just in case we end up needing them
    getText(){
        return this.text;
    }

    getNoteID(){
        return this.noteID;
    }
} // end Note class


// Generate NoteSheets and add Notes to them
myNoteSheet = new NoteSheet(1000, 1111);
note1 = new Note(myNoteSheet, 1011, 1101, "This is the first new note");
note2 = new Note(myNoteSheet, 1010, 1110, "This is the second new note");
note3 = new Note(myNoteSheet, 1110, 1000, "This is the third new note");
note4 = new Note(myNoteSheet, 1101, 1001, "This is the fourth new note");
note5 = new Note(myNoteSheet, 1011, 1110, "This is the fifth new note");

myNoteSheet.printNotes();
console.log("\nPrinting note1:");
console.log(note1);

// Get the noteSheetID for the NoteSheet that the Note belongs to
console.log("\nPrinting the noteSheetID that note1 belongs to:");
console.log(note1.noteSheet.noteSheetID);

myNoteSheet2 = new NoteSheet(1011, 1001);
note6 = new Note(myNoteSheet2, 1111, 2000, "This is the first note for this notesheet");
note7 = new Note(myNoteSheet2, 1001, 2001, "This is the second note for this notesheet");
note8 = new Note(myNoteSheet2, 1100, 2002, "This is the third note for this notesheet");
note9 = new Note(myNoteSheet2, 1011, "This is the fourth note for this notesheet");

myNoteSheet2.printNotes();

// still need an EDIT note method?
// do we also want a DELETE note method?

// Thoughts: Whenever a Lab is created, it (the Lab constructor?) creates a New Notesheet for the lab right away

