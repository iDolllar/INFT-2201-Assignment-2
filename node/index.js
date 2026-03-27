/**
 * Assignment 2 – Authentication, Authorization, and RBAC
 * Student: Aadithkeshev Anushayamunaithuraivan
 * Course: INFT-2201
 * Date: March 27th, 2026
 */
import http from "http";
import fs from "fs";
import jwt from "jsonwebtoken";

const JWT_SECRET = "a2_9f4c1d7e3b8a6f2c5d1e9b7a3c8f4e";

http
  .createServer((req, res) => {
    if (req.method === "GET") {
      res.writeHead(200, { "Content-Type": "text/plain" });
      res.end("Hello Apache!\n");
      return;
    }

    if (req.method === "POST") {
      if (req.url === "/login") {
        let body = "";

        req.on("data", (chunk) => {
          body += chunk;
        });

        req.on("end", () => {
          try {
            body = JSON.parse(body);

            if (!body.username || !body.password) {
              res.writeHead(400, { "Content-Type": "application/json" });
              res.end(JSON.stringify({ error: "username and password required" }));
              return;
            }
             // Read users from users.txt and validate credentials
              // If valid, generate JWT with userId and role
            const fileContents = fs.readFileSync("./users.txt", "utf8");
            const lines = fileContents
              .split("\n")
              .map((line) => line.trim())
              .filter((line) => line.length > 0);

            let matchedUser = null;

            for (const line of lines) {
              const parts = line.split(",");

              // users.txt format:
              // userId,username,password,role
              if (parts.length !== 4) {
                continue;
              }

              const [userId, username, password, role] = parts;

              if (username === body.username) {
                matchedUser = {
                  userId: Number(userId),
                  username,
                  password,
                  role,
                };
                break;
              }
            }

            if (!matchedUser) {
              res.writeHead(404, { "Content-Type": "text/plain" });
              res.end(`${body.username} not found\n`);
              return;
            }

            if (matchedUser.password !== body.password) {
              res.writeHead(401, { "Content-Type": "text/plain" });
              res.end("Invalid password\n");
              return;
            }

            const token = jwt.sign(
              {
                userId: matchedUser.userId,
                role: matchedUser.role,
              },
              JWT_SECRET,
              { expiresIn: "1h" }
            );

            res.writeHead(200, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ token }));
          } catch (err) {
            console.log(err);
            res.writeHead(500, { "Content-Type": "text/plain" });
            res.end("Server error\n");
          }
        });

        return;
      }

      res.writeHead(404, { "Content-Type": "text/plain" });
      res.end("Not found\n");
      return;
    }

    res.writeHead(404, { "Content-Type": "text/plain" });
    res.end("Not found\n");
  })
  .listen(8000);

console.log("listening on port 8000");