import mitt from "mitt";

export const FILE_UPLOAD_STARTED = "FILE_UPLOAD_STARTED";

export const emitter = mitt();

// listen to an event
// emitter.on("foo", (e) => console.log("foo", e));
