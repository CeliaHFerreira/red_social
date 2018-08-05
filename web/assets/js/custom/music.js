// Number of steps to play each chord.
STEPS_PER_CHORD = 1;
STEPS_PER_PROG = 4 * STEPS_PER_CHORD;

// Number of times to repeat chord progression.
NUM_REPS = 12;

// Set up Improv RNN model and player.
const model = new mm.MusicRNN('https://storage.googleapis.com/magentadata/js/checkpoints/music_rnn/chord_pitches_improv');

const player = new mm.Player();
var save = false;
var playing = false;

// Current chords being played.
var currentChords = undefined;

let seq = {
	quantizationInfo: {stepsPerQuarter: 4},
	notes: [],
	totalQuantizedSteps: 1
};

// Sample over chord progression.
function playOnce() {
	seq = {
		quantizationInfo: {stepsPerQuarter: 4},
		notes: [],
		totalQuantizedSteps: 1
	};
	currentChords = [
		document.getElementById('chord1').value,
		document.getElementById('chord2').value,
		document.getElementById('chord3').value,
		document.getElementById('chord4').value
	];
	checkChords();
	const chords = currentChords;

	// Prime with root note of the first chord.
	const root = mm.chords.ChordSymbols.root(chords[0]);


	document.getElementById('message').innerText = 'Improvising over: ' + chords;
	model.continueSequence(seq, STEPS_PER_PROG + (NUM_REPS - 1) * STEPS_PER_PROG - 1, 0.9, chords)
			.then((contSeq) => {
				// Add the continuation to the original.
				contSeq.notes.forEach((note) => {
					note.quantizedStartStep += 1;
					note.quantizedEndStep += 1;
					seq.notes.push(note);
				});

				const roots = chords.map(mm.chords.ChordSymbols.root);
				for (var i = 0; i < NUM_REPS; i++) {
					// Add the bass progression.
					seq.notes.push({
						instrument: 1,
						program: 32,
						pitch: 36 + roots[0],
						quantizedStartStep: i * STEPS_PER_PROG,
						quantizedEndStep: i * STEPS_PER_PROG + STEPS_PER_CHORD
					});
					seq.notes.push({
						instrument: 1,
						program: 32,
						pitch: 36 + roots[1],
						quantizedStartStep: i * STEPS_PER_PROG + STEPS_PER_CHORD,
						quantizedEndStep: i * STEPS_PER_PROG + 2 * STEPS_PER_CHORD
					});
					seq.notes.push({
						instrument: 1,
						program: 32,
						pitch: 36 + roots[2],
						quantizedStartStep: i * STEPS_PER_PROG + 2 * STEPS_PER_CHORD,
						quantizedEndStep: i * STEPS_PER_PROG + 3 * STEPS_PER_CHORD
					});
					seq.notes.push({
						instrument: 1,
						program: 32,
						pitch: 36 + roots[3],
						quantizedStartStep: i * STEPS_PER_PROG + 3 * STEPS_PER_CHORD,
						quantizedEndStep: i * STEPS_PER_PROG + 4 * STEPS_PER_CHORD
					});
				}

				// Set total sequence length.
				seq.totalQuantizedSteps = STEPS_PER_PROG * NUM_REPS;



				// Play it!
				player.start(seq, 120).then(() => {
					playing = false;


					document.getElementById('message').innerText = 'Change chords and play again!';
					checkChords();
				});

			})
}//fin playOnce()




// Check chords for validity and highlight invalid chords.
const checkChords = () => {
	const chords = [
		document.getElementById('chord1').value,
		document.getElementById('chord2').value,
		document.getElementById('chord3').value,
		document.getElementById('chord4').value
	];

	const isGood = (chord) => {
		if (!chord) {
			return false;
		}
		try {
			mm.chords.ChordSymbols.pitches(chord);
			return true;
		} catch (e) {
			return false;
		}
	}

	var allGood = true;
	if (isGood(chords[0])) {
		document.getElementById('chord1').style.color = 'black';
	} else {
		document.getElementById('chord1').style.color = 'red';
		allGood = false;
	}
	if (isGood(chords[1])) {
		document.getElementById('chord2').style.color = 'black';
	} else {
		document.getElementById('chord2').style.color = 'red';
		allGood = false;
	}
	if (isGood(chords[2])) {
		document.getElementById('chord3').style.color = 'black';
	} else {
		document.getElementById('chord3').style.color = 'red';
		allGood = false;
	}
	if (isGood(chords[3])) {
		document.getElementById('chord4').style.color = 'black';
	} else {
		document.getElementById('chord4').style.color = 'red';
		allGood = false;
	}

	var changed = false;
	if (currentChords) {
		if (chords[0] !== currentChords[0]) {
			changed = true;
		}
		if (chords[1] !== currentChords[1]) {
			changed = true;
		}
		if (chords[2] !== currentChords[2]) {
			changed = true;
		}
		if (chords[3] !== currentChords[3]) {
			changed = true;
		}
	} else {
		changed = true;
	}
	document.getElementById('play').disabled = !allGood || (!changed && playing);
}

// Check chords for validity when changed.
document.getElementById('chord1').oninput = checkChords;
document.getElementById('chord2').oninput = checkChords;
document.getElementById('chord3').oninput = checkChords;
document.getElementById('chord4').oninput = checkChords;



function saveSequence() {
	if (!seq) {
		alert('You must generate a trio before you can download it!');
	} else {
		const midi = mm.sequenceProtoToMidi(seq);
		//saveAs(new File([mm.sequenceProtoToMidi(seq)], 'prueba.mid'));;
		const file = new Blob([midi], {type: 'audio/midi'});

		if (window.navigator.msSaveOrOpenBlob) {
			window.navigator.msSaveOrOpenBlob(file, 'prueba.mid');
		} 
		else { // Others
			var myURL = URL || window.webkitURL;
			const a = document.createElement('a');
			const url = myURL.createObjectURL(file);
			a.href = url;
			a.download = 'prog.mid';
			document.body.appendChild(a);
			a.click();
			setTimeout(() => {
				document.body.removeChild(a);
				myURL.revokeObjectURL(url);
			}
			, 0);
		}
	}
}

play.onclick = playOnce;
download.onclick = saveSequence;

// Initialize model then start playing.
model.initialize().then(() => {
	document.getElementById('message').innerText = 'Done loading model.'
	document.getElementById('play').disabled = false;
	document.getElementById('download').disabled = false;
});