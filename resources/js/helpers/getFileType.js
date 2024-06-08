function getFileType(mime) {
    switch (mime) {
        case "application/pdf":
        case "application/x-pdf":
        case "application/acrobat":
        case "applications/vnd.pdf":
        case "text/pdf":
        case "text/x-pdf":
            return "pdf";

        case "application/msword":
        case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
        case "application/vnd.ms-word.document.macrosEnabled.12":
        case "application/vnd.ms-word.template.macroEnabled.12":
            return "doc";

        case "application/vnd.ms-excel":
        case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
        case "application/vnd.ms-excel.sheet.macroEnabled.12":
        case "application/vnd.ms-excel.template.macroEnabled.12":
            return "xls";

        case "application/vnd.ms-powerpoint":
        case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
        case "application/vnd.ms-powerpoint.presentation.macroEnabled.12":
        case "application/vnd.ms-powerpoint.slideshow.macroEnabled.12":
            return "ppt";

        case "text/plain":
        case "text/csv":
        case "text/tab-separated-values":
        case "text/calendar":
        case "text/x-vcalendar":
        case "text/x-vcard":
            return "txt";

        case "image/jpeg":
        case "image/png":
        case "image/gif":
            return "img";

        case "audio/mpeg":
        case "audio/ogg":
        case "audio/wav":
        case "audio/webm":
        case "audio/x-m4a":
        case "audio/x-wav":
            return "audio";

        case "video/mp4":
        case "video/ogg":
        case "video/webm":
        case "video/mpeg":
        case "video/mkv":
        case "video/quicktime":
            return "video";

        case "application/zip":
        case "application/x-zip-compressed":
        case "application/x-compressed":
            return "zip";

        default:
            return "folder";
    }
}

export default getFileType;
