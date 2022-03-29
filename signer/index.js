const { sign } = require("jsonwebtoken");
const { readFileSync } = require("fs");
const { v4 } = require("uuid");
const { config } = require("dotenv");

config();

const { TENANT, CLIENT_ID, THUMBPRINT, EXPIRES_DAYS, PASSPHRASE } = process.env;

const octets = THUMBPRINT.match(/.{1,2}/g);
const buffer = Buffer.alloc(octets.length);

for (let i = 0; i < octets.length; i++) {
  buffer.writeUInt8(parseInt(octets[i], 16), i);
}

const x5t = buffer
  .toString("base64")
  .replace(/=/g, "")
  .replace(/\+/g, "-")
  .replace(/\//g, "_");

// Current timestamp in seconds
const nowTimestamp = Math.floor(Date.now() / 1000);

const expiringIn = nowTimestamp + 86400 * +EXPIRES_DAYS;

const payload = {
  aud: `https://login.microsoftonline.com/${TENANT}/oauth2/v2.0/token`,
  exp: expiringIn,
  iss: CLIENT_ID,
  jti: v4(),
  nbf: nowTimestamp,
  sub: CLIENT_ID,
};

const token = sign(
  payload,
  { key: readFileSync("cert/key.pem").toString(), passphrase: PASSPHRASE },
  {
    algorithm: "RS256",
    header: { x5t },
  }
);

console.log(token);
