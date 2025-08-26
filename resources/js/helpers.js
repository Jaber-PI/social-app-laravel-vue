import { formatDistanceToNow } from 'date-fns'


export const isImage = (attachment) => {
    let mime = attachment.mime || attachment.type;
    mime = mime.split('/');
    return mime[0].toLowerCase() === "image";
}

export const formatDate = (iso) => {
    return formatDistanceToNow(new Date(iso), { addSuffix: true })
}
