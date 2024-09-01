import { assertEquals } from "jsr:@std/assert";

Deno.test("Upload", async () => {
	// Load image
	const imageData = await Deno.readFile("./test/test.png");
	const imageBlob = new Blob([imageData.buffer], { type: "image/png" });

	// Upload image
	const updateResponse = await fetch("http://localhost:8080/upload", {
		method: "POST",
		body: imageBlob,
	});

	console.log(updateResponse);

	const updateData = await updateResponse.json();

	console.log(updateData);

	assertEquals(updateData.success, true);
	assertEquals(/^\d+$/.test(updateData.id), true);
});

Deno.test("Download", async () => {
	const downloadResponse = await fetch("http://localhost:8080/1");

	console.log(downloadResponse);

	const blob = await downloadResponse.blob();

	assertEquals(blob.type, "image/png");

	assertEquals(downloadResponse.ok, true);
});

Deno.test("Nomal operation", async () => {
	// Load image
	const imageData = await Deno.readFile("./test/test.png");
	const imageBlob = new Blob([imageData.buffer], { type: "image/png" });

	// Upload image
	const updateResponse = await fetch("http://localhost:8080/upload", {
		method: "POST",
		body: imageBlob,
	});
	const updateData = await updateResponse.json();

	console.log(updateData);

	assertEquals(updateData.success, true);

	// Get id
	const id = updateData.id;

	// Download image
	const downloadResponse = await fetch(`http://localhost:8080/${id}`);

	console.log(downloadResponse);

	const downloadBlob = await downloadResponse.blob();

	// Check image
	const downloadData = await downloadBlob.arrayBuffer();
	const downloadArray = new Uint8Array(downloadData);
	const imageArray = new Uint8Array(imageData.buffer);
	assertEquals(downloadArray, imageArray);
});

Deno.test("Invalid", async () => {
	const downloadResponse = await fetch("http://localhost:8080/invalid");

	// 404 Not Found
	assertEquals(downloadResponse.status, 404);
	await downloadResponse.text();
});

Deno.test("Large file", async () => {
	const imageData = await Deno.readFile("./test/test_large.png");
	const largeImageBlob = new Blob([imageData.buffer], { type: "image/png" });

	const updateResponse = await fetch("http://localhost:8080/upload", {
		method: "POST",
		body: largeImageBlob,
	});

	console.log(updateResponse);

	const updateData = await updateResponse.json();

	console.log(updateData);

	assertEquals(updateData.success, false);
	// "large" includes in error message
	assertEquals(updateData.message.includes("large"), true);
});

Deno.test("Not Found", async () => {
	const downloadResponse = await fetch("http://localhost:8080/1000000");

	await downloadResponse.text();

	assertEquals(downloadResponse.status, 404);
});
