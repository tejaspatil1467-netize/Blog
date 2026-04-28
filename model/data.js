// model/data.js — Dummy blog data (simulates a backend)

const blogs = [
  {
    id: 1,
    title: "Mastering Java Streams: A Deep Dive into Functional Programming",
    excerpt: "Explore how Java 8 Streams transform collection processing with declarative, functional-style pipelines — boosting readability and performance.",
    author: "Rohan Jadhav",
    authorAvatar: "RJ",
    authorBio: "Full Stack Developer passionate about Java, Spring Boot & clean code.",
    date: "April 18, 2026",
    tags: ["Java", "Functional", "Streams"],
    readTime: "7 min read",
    coverColor: "#0f172a",
    content: `
      <h2>Introduction</h2>
      <p>Java Streams, introduced in Java 8, revolutionize the way developers process collections of data. Instead of verbose <code>for</code> loops, you write expressive pipelines that are both readable and powerful.</p>

      <h2>What is a Stream?</h2>
      <p>A Stream is a sequence of elements supporting sequential and parallel aggregate operations. It is <strong>not</strong> a data structure — it computes on demand from a source.</p>

      <pre><code>List&lt;String&gt; names = List.of("Alice", "Bob", "Charlie", "Dave");

// Filter names starting with 'A' and convert to uppercase
List&lt;String&gt; result = names.stream()
    .filter(name -&gt; name.startsWith("A"))
    .map(String::toUpperCase)
    .collect(Collectors.toList());

System.out.println(result); // [ALICE]</code></pre>

      <h2>Core Stream Operations</h2>
      <p>Stream operations are divided into <strong>intermediate</strong> (lazy, return a stream) and <strong>terminal</strong> (eager, produce a result).</p>

      <h3>Intermediate Operations</h3>
      <ul>
        <li><code>filter(Predicate)</code> — Keeps elements matching a condition</li>
        <li><code>map(Function)</code> — Transforms each element</li>
        <li><code>flatMap(Function)</code> — Flattens nested streams</li>
        <li><code>sorted()</code> — Sorts elements</li>
        <li><code>distinct()</code> — Removes duplicates</li>
      </ul>

      <h3>Terminal Operations</h3>
      <ul>
        <li><code>collect()</code> — Gathers elements into a collection</li>
        <li><code>forEach()</code> — Iterates over each element</li>
        <li><code>reduce()</code> — Aggregates elements</li>
        <li><code>count()</code> — Returns count of elements</li>
        <li><code>anyMatch() / allMatch()</code> — Tests predicates</li>
      </ul>

      <h2>Real-World Example: Grouping Employees by Department</h2>
      <pre><code>Map&lt;String, List&lt;Employee&gt;&gt; byDept = employees.stream()
    .collect(Collectors.groupingBy(Employee::getDepartment));

byDept.forEach((dept, emps) -&gt; {
    System.out.println(dept + ": " + emps.size() + " employees");
});</code></pre>

      <h2>Performance: Parallel Streams</h2>
      <p>For CPU-intensive tasks on large datasets, parallel streams distribute work across multiple threads:</p>
      <pre><code>long count = LongStream.rangeClosed(1, 10_000_000)
    .parallel()
    .filter(n -&gt; n % 2 == 0)
    .count();

System.out.println("Even numbers: " + count); // 5000000</code></pre>

      <h2>Conclusion</h2>
      <p>Java Streams enable a more expressive, functional style of programming. Master them and your code will be cleaner, more maintainable, and often faster.</p>
    `
  },
  {
    id: 2,
    title: "Building Scalable REST APIs with Spring Boot 3",
    excerpt: "A hands-on guide to designing production-ready REST APIs using Spring Boot 3, validation, exception handling, and Swagger documentation.",
    author:"Tejas Patil",
    authorAvatar: "PS",
    authorBio: "Backend engineer specializing in Spring ecosystem and microservices.",
    date: "April 15, 2026",
    tags: ["Spring Boot", "REST API", "Java"],
    readTime: "10 min read",
    coverColor: "#064e3b",
    content: `
      <h2>Why Spring Boot?</h2>
      <p>Spring Boot eliminates boilerplate configuration, letting you spin up a production-ready server in minutes. With Spring Boot 3, you get native GraalVM support, improved observability, and a modernized security model.</p>

      <h2>Project Setup</h2>
      <pre><code>// pom.xml dependencies
&lt;dependency&gt;
    &lt;groupId&gt;org.springframework.boot&lt;/groupId&gt;
    &lt;artifactId&gt;spring-boot-starter-web&lt;/artifactId&gt;
&lt;/dependency&gt;
&lt;dependency&gt;
    &lt;groupId&gt;org.springframework.boot&lt;/groupId&gt;
    &lt;artifactId&gt;spring-boot-starter-validation&lt;/artifactId&gt;
&lt;/dependency&gt;</code></pre>

      <h2>Creating a REST Controller</h2>
      <pre><code>@RestController
@RequestMapping("/api/v1/users")
public class UserController {

    private final UserService userService;

    @GetMapping("/{id}")
    public ResponseEntity&lt;UserDTO&gt; getUser(@PathVariable Long id) {
        return ResponseEntity.ok(userService.findById(id));
    }

    @PostMapping
    public ResponseEntity&lt;UserDTO&gt; createUser(
            @Valid @RequestBody CreateUserRequest request) {
        UserDTO created = userService.create(request);
        URI location = URI.create("/api/v1/users/" + created.getId());
        return ResponseEntity.created(location).body(created);
    }
}</code></pre>

      <h2>Global Exception Handling</h2>
      <pre><code>@RestControllerAdvice
public class GlobalExceptionHandler {

    @ExceptionHandler(ResourceNotFoundException.class)
    public ProblemDetail handleNotFound(ResourceNotFoundException ex) {
        ProblemDetail pd = ProblemDetail.forStatus(HttpStatus.NOT_FOUND);
        pd.setDetail(ex.getMessage());
        return pd;
    }
}</code></pre>

      <h2>Conclusion</h2>
      <p>Spring Boot 3 gives you a powerful, opinionated foundation. Layer in validation, consistent error responses, and OpenAPI docs — and you have an API ready for production.</p>
    `
  },
  {
    id: 3,
    title: "React 19 Deep Dive: Concurrent Features & Server Components",
    excerpt: "React 19 brings game-changing concurrent rendering, use() hook, and Server Components that fundamentally change how we build UIs.",
    author: "Pruthviraj Patil",
    authorAvatar: "AP",
    authorBio: "Frontend architect building high-performance React applications.",
    date: "April 12, 2026",
    tags: ["React", "JavaScript", "Frontend"],
    readTime: "9 min read",
    coverColor: "#1e3a5f",
    content: `
      <h2>React 19 is Here</h2>
      <p>React 19 is the most significant release in years. It brings server components to stable, introduces the <code>use()</code> hook, and supercharges concurrent rendering.</p>

      <h2>The use() Hook</h2>
      <p>The new <code>use()</code> hook lets you read the value of a Promise or Context inside render — without <code>useEffect</code>:</p>
      <pre><code>import { use, Suspense } from 'react';

function UserCard({ userPromise }) {
  const user = use(userPromise); // Suspends until resolved
  return &lt;div&gt;{user.name}&lt;/div&gt;;
}

export default function App() {
  const userPromise = fetchUser(1);
  return (
    &lt;Suspense fallback={&lt;Spinner /&gt;}&gt;
      &lt;UserCard userPromise={userPromise} /&gt;
    &lt;/Suspense&gt;
  );
}</code></pre>

      <h2>Server Components</h2>
      <p>Server Components run only on the server, reducing JavaScript bundle size and enabling direct database access:</p>
      <pre><code>// app/page.tsx — Server Component (no 'use client')
async function BlogList() {
  const posts = await db.posts.findMany(); // Direct DB call!
  return posts.map(p =&gt; &lt;BlogCard key={p.id} post={p} /&gt;);
}</code></pre>

      <h2>Optimistic Updates with useOptimistic</h2>
      <pre><code>const [optimisticLikes, addOptimisticLike] = useOptimistic(
  likes,
  (state, newLike) =&gt; [...state, newLike]
);

async function handleLike() {
  addOptimisticLike(tempLike); // Instantly updates UI
  await saveLikeToServer(tempLike); // Then confirms
}</code></pre>

      <h2>Conclusion</h2>
      <p>React 19 blurs the boundary between client and server, enabling patterns that were previously impossible without complex frameworks.</p>
    `
  },
  {
    id: 4,
    title: "AI-Powered Code Reviews: How LLMs Are Changing Engineering Teams",
    excerpt: "From PR summaries to security scanning, AI is automating the most tedious parts of code review — here's what your team needs to know.",
    author: "Sneha Kulkarni",
    authorAvatar: "SK",
    authorBio: "DevOps engineer & AI enthusiast exploring the future of software delivery.",
    date: "April 10, 2026",
    tags: ["AI", "DevOps", "LLM"],
    readTime: "6 min read",
    coverColor: "#2d1b69",
    content: `
      <h2>The Problem with Manual Code Review</h2>
      <p>Code review is critical but expensive. Engineers spend 10–15% of their time reviewing code — time that could go toward building features.</p>

      <h2>What AI Can Do Today</h2>
      <ul>
        <li><strong>PR Summaries</strong> — Auto-generate plain-English descriptions of what changed and why</li>
        <li><strong>Bug Detection</strong> — Spot common patterns like null pointer risks, SQL injection, race conditions</li>
        <li><strong>Style Enforcement</strong> — Suggest improvements aligned with your team's coding standards</li>
        <li><strong>Test Coverage Gaps</strong> — Identify uncovered code paths and suggest test cases</li>
      </ul>

      <h2>Tools Leading the Space</h2>
      <pre><code># Example: CodeRabbit configuration (.coderabbit.yaml)
reviews:
  auto_review:
    enabled: true
    drafts: false
  path_filters:
    - "!**/node_modules/**"
    - "!**/*.lock"
language: "en-US"
tone_instructions: "Be concise and constructive"</code></pre>

      <h2>The Human Element</h2>
      <p>AI reviews are not replacements — they're force multipliers. They handle the mechanical so humans can focus on architecture, intent, and team knowledge-sharing.</p>

      <h2>Conclusion</h2>
      <p>Teams that adopt AI in their review workflow ship faster with fewer bugs. The question isn't if — it's when.</p>
    `
  },
  {
    id: 5,
    title: "TypeScript Generics: Write Once, Use Everywhere",
    excerpt: "Generics unlock the full power of TypeScript's type system. Learn to write reusable, type-safe utilities that work across your entire codebase.",
    author: "Karan Mahadik",
    authorAvatar: "KM",
    authorBio: "TypeScript advocate building design systems and internal tooling.",
    date: "April 8, 2026",
    tags: ["TypeScript", "JavaScript", "Types"],
    readTime: "8 min read",
    coverColor: "#1a3a4a",
    content: `
      <h2>What Are Generics?</h2>
      <p>Generics let you write components that work with <em>any</em> type while still enforcing type correctness. Think of them as type-level functions.</p>

      <h2>Basic Generic Function</h2>
      <pre><code>function identity&lt;T&gt;(value: T): T {
  return value;
}

const num = identity&lt;number&gt;(42);   // type: number
const str = identity("hello");       // type: string (inferred)</code></pre>

      <h2>Generic Constraints</h2>
      <pre><code>function getLength&lt;T extends { length: number }&gt;(item: T): number {
  return item.length;
}

getLength("hello");     // ✅ 5
getLength([1, 2, 3]);   // ✅ 3
getLength(42);           // ❌ Error: number has no 'length'</code></pre>

      <h2>Building a Type-Safe API Client</h2>
      <pre><code>async function fetchData&lt;T&gt;(url: string): Promise&lt;T&gt; {
  const res = await fetch(url);
  if (!res.ok) throw new Error(\`HTTP \${res.status}\`);
  return res.json() as Promise&lt;T&gt;;
}

interface User { id: number; name: string; }
const user = await fetchData&lt;User&gt;('/api/users/1');
console.log(user.name); // Fully typed ✅</code></pre>

      <h2>Mapped Types with Generics</h2>
      <pre><code>type Optional&lt;T&gt; = {
  [K in keyof T]?: T[K];
};

type RequiredFields&lt;T, K extends keyof T&gt; = 
  Required&lt;Pick&lt;T, K&gt;&gt; & Partial&lt;Omit&lt;T, K&gt;&gt;;

// Make only 'id' and 'email' required in User
type CreateUser = RequiredFields&lt;User, 'email'&gt;;</code></pre>

      <h2>Conclusion</h2>
      <p>Generics are the bridge between flexibility and type safety. Once you internalize them, you'll wonder how you ever wrote TypeScript without them.</p>
    `
  },
  {
    id: 6,
    title: "Docker & Kubernetes: Containerizing a Spring Boot Microservice",
    excerpt: "From Dockerfile to K8s deployment — a complete walkthrough of containerizing a Spring Boot service and running it on Kubernetes.",
    author: "soham Desai",
    authorAvatar: "AD",
    authorBio: "Cloud architect specializing in container orchestration and CI/CD pipelines.",
    date: "April 5, 2026",
    tags: ["Docker", "Kubernetes", "DevOps"],
    readTime: "12 min read",
    coverColor: "#0c3547",
    content: `
      <h2>Why Containerize?</h2>
      <p>Containers eliminate "works on my machine" problems by packaging your application with all its dependencies into a portable, immutable unit.</p>

      <h2>Writing an Optimized Dockerfile</h2>
      <pre><code># Multi-stage build for minimal image size
FROM eclipse-temurin:21-jdk-jammy AS builder
WORKDIR /app
COPY . .
RUN ./mvnw package -DskipTests

FROM eclipse-temurin:21-jre-jammy
WORKDIR /app
COPY --from=builder /app/target/*.jar app.jar
EXPOSE 8080
ENTRYPOINT ["java", "-jar", "app.jar"]</code></pre>

      <h2>Kubernetes Deployment</h2>
      <pre><code>apiVersion: apps/v1
kind: Deployment
metadata:
  name: user-service
spec:
  replicas: 3
  selector:
    matchLabels:
      app: user-service
  template:
    metadata:
      labels:
        app: user-service
    spec:
      containers:
      - name: user-service
        image: myregistry/user-service:1.0.0
        ports:
        - containerPort: 8080
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"</code></pre>

      <h2>Health Checks</h2>
      <pre><code>livenessProbe:
  httpGet:
    path: /actuator/health/liveness
    port: 8080
  initialDelaySeconds: 30
readinessProbe:
  httpGet:
    path: /actuator/health/readiness
    port: 8080
  initialDelaySeconds: 15</code></pre>

      <h2>Conclusion</h2>
      <p>Containerization with Docker and Kubernetes gives you scalability, resilience, and consistent deployments — the foundation of modern cloud-native engineering.</p>
    `
  },
  {
    id: 7,
    title: "Understanding System Design: Designing a URL Shortener at Scale",
    excerpt: "Walk through the complete system design of a URL shortener — from hash generation to database sharding and CDN caching.",
    author: "Vikram Nair",
    authorAvatar: "VN",
    authorBio: "Principal engineer obsessed with distributed systems and scalability.",
    date: "April 2, 2026",
    tags: ["System Design", "Architecture", "Scalability"],
    readTime: "11 min read",
    coverColor: "#1c1c2e",
    content: `
      <h2>Requirements</h2>
      <p><strong>Functional:</strong> Shorten a URL, redirect to original. <strong>Non-functional:</strong> 100M+ URLs, < 50ms read latency, 99.99% uptime.</p>

      <h2>Hash Generation Strategy</h2>
      <pre><code>// Base62 encoding for short codes
const BASE62 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

function encodeBase62(num) {
  let result = "";
  while (num > 0) {
    result = BASE62[num % 62] + result;
    num = Math.floor(num / 62);
  }
  return result.padStart(7, '0');
}

// ID 1000000 → "4c92"
console.log(encodeBase62(1000000));</code></pre>

      <h2>Database Design</h2>
      <pre><code>CREATE TABLE urls (
  id         BIGINT PRIMARY KEY AUTO_INCREMENT,
  short_code VARCHAR(10) UNIQUE NOT NULL,
  long_url   TEXT NOT NULL,
  user_id    BIGINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP,
  click_count BIGINT DEFAULT 0
);

-- Shard key: short_code % 16 for 16 DB shards</code></pre>

      <h2>Caching Layer</h2>
      <p>Cache the hot 20% of URLs that drive 80% of traffic in Redis with TTL:</p>
      <pre><code># Redis cache structure
SET url:abc1234 "https://example.com/very/long/path" EX 86400

# On redirect request:
1. Check Redis cache → HIT: redirect instantly
2. MISS: Query DB → cache result → redirect</code></pre>

      <h2>Conclusion</h2>
      <p>A URL shortener may seem simple, but designing it at scale teaches every core system design concept: hashing, caching, sharding, and load balancing.</p>
    `
  },
  {
    id: 8,
    title: "Git Internals: How Git Actually Works Under the Hood",
    excerpt: "Forget memorizing commands — understand what Git is actually doing: objects, trees, commits, and refs explained from first principles.",
    author: "Meera Joshi",
    authorAvatar: "MJ",
    authorBio: "Open source contributor and engineering educator at heart.",
    date: "March 30, 2026",
    tags: ["Git", "DevTools", "CS Fundamentals"],
    readTime: "8 min read",
    coverColor: "#2d1200",
    content: `
      <h2>Git is a Content-Addressable Store</h2>
      <p>At its core, Git stores everything as objects identified by their SHA-1 hash. There are four object types: <strong>blob</strong>, <strong>tree</strong>, <strong>commit</strong>, and <strong>tag</strong>.</p>

      <h2>Exploring Objects</h2>
      <pre><code># Hash any content
echo "Hello, Git!" | git hash-object --stdin
# → 8ab686eafeb1f44702738c8b0f24f2567c36da6d

# Store it in Git's object store
echo "Hello, Git!" | git hash-object -w --stdin

# Retrieve it
git cat-file -p 8ab686ea
# → Hello, Git!</code></pre>

      <h2>How a Commit is Structured</h2>
      <pre><code>git cat-file -p HEAD

# Output:
tree a1b2c3d4...         ← points to root tree object
parent 9f8e7d6c...        ← previous commit
author Rohan &lt;r@x.com&gt; 1714000000 +0530
committer Rohan &lt;r@x.com&gt; 1714000000 +0530

Add user authentication</code></pre>

      <h2>What git commit Really Does</h2>
      <ol>
        <li>Hashes each changed file → creates <strong>blob objects</strong></li>
        <li>Hashes directory structure → creates <strong>tree objects</strong></li>
        <li>Creates a <strong>commit object</strong> pointing to root tree + parent</li>
        <li>Updates the branch ref (e.g., <code>.git/refs/heads/main</code>) to new commit hash</li>
      </ol>

      <h2>Conclusion</h2>
      <p>Once you see Git as a persistent hash map of objects, every command—rebase, merge, cherry-pick—becomes intuitive rather than magical.</p>
    `
  },
  {
    id: 9,
    title: "CSS Grid vs Flexbox: When to Use Which (With Real Examples)",
    excerpt: "Stop guessing — learn the mental model that tells you exactly when to reach for CSS Grid vs Flexbox, with practical, copy-paste examples.",
    author: "Tanya Reddy",
    authorAvatar: "TR",
    authorBio: "UI engineer focused on design systems, accessibility, and CSS architecture.",
    date: "March 27, 2026",
    tags: ["CSS", "Frontend", "Web Design"],
    readTime: "7 min read",
    coverColor: "#0f2027",
    content: `
      <h2>The One-Line Rule</h2>
      <p>Use <strong>Flexbox</strong> for one-dimensional layouts (row OR column). Use <strong>Grid</strong> for two-dimensional layouts (rows AND columns simultaneously).</p>

      <h2>Flexbox in Action: Navigation Bar</h2>
      <pre><code>.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 2rem;
  gap: 1rem;
}

/* Logo left, nav links center, button right */
.nav-logo   { flex: 0 0 auto; }
.nav-links  { flex: 1; display: flex; gap: 2rem; justify-content: center; }
.nav-cta    { flex: 0 0 auto; }</code></pre>

      <h2>Grid in Action: Blog Card Layout</h2>
      <pre><code>.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
}

/* Card with internal grid layout */
.blog-card {
  display: grid;
  grid-template-rows: auto 1fr auto;
  /* Header, content (grows), footer */
}</code></pre>

      <h2>Grid for Complex Layouts</h2>
      <pre><code>.page-layout {
  display: grid;
  grid-template-areas:
    "header  header  header"
    "sidebar content aside"
    "footer  footer  footer";
  grid-template-columns: 250px 1fr 200px;
  grid-template-rows: 64px 1fr 80px;
  min-height: 100vh;
}</code></pre>

      <h2>When to Combine Both</h2>
      <p>Use Grid for page-level structure. Use Flexbox inside Grid cells for component-level alignment. They complement each other perfectly.</p>

      <h2>Conclusion</h2>
      <p>Both are essential. Grid gives you the layout skeleton; Flexbox gives you alignment superpowers inside each cell.</p>
    `
  },
  {
    id: 10,
    title: "PostgreSQL Performance Tuning: From Slow Queries to Sub-Millisecond",
    excerpt: "Real-world strategies to diagnose and fix slow PostgreSQL queries using EXPLAIN ANALYZE, indexing strategies, and query optimization.",
    author: "Rohit Verma",
    authorAvatar: "RV",
    authorBio: "Database engineer with expertise in PostgreSQL, query optimization, and data modeling.",
    date: "March 24, 2026",
    tags: ["PostgreSQL", "Database", "Performance"],
    readTime: "10 min read",
    coverColor: "#0a1628",
    content: `
      <h2>The Investigation Starts Here</h2>
      <p>Always begin with <code>EXPLAIN ANALYZE</code> — it shows the actual query plan, row estimates, and timing data.</p>

      <pre><code>EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT u.name, COUNT(o.id) as order_count
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE u.created_at > NOW() - INTERVAL '30 days'
GROUP BY u.id, u.name
ORDER BY order_count DESC
LIMIT 20;</code></pre>

      <h2>Reading the Query Plan</h2>
      <p>Look for these red flags:</p>
      <ul>
        <li><strong>Seq Scan</strong> on large tables — missing index</li>
        <li><strong>Rows estimate off by 10x+</strong> — stale statistics, run ANALYZE</li>
        <li><strong>Hash Join spilling to disk</strong> — increase <code>work_mem</code></li>
        <li><strong>Nested Loop on large sets</strong> — consider a Hash Join</li>
      </ul>

      <h2>Index Strategies</h2>
      <pre><code>-- Partial index: only index active users
CREATE INDEX idx_active_users 
ON users(email) 
WHERE is_active = true;

-- Composite index: column order matters!
CREATE INDEX idx_orders_user_date 
ON orders(user_id, created_at DESC);

-- Covering index: avoid heap fetch entirely
CREATE INDEX idx_covering 
ON orders(user_id) INCLUDE (total, status);</code></pre>

      <h2>Configuration Tuning</h2>
      <pre><code># postgresql.conf — production settings
shared_buffers = 4GB          # 25% of RAM
effective_cache_size = 12GB   # 75% of RAM
work_mem = 64MB               # Per sort/hash operation
maintenance_work_mem = 1GB    # For VACUUM, CREATE INDEX
random_page_cost = 1.1        # For SSD storage
enable_partitionwise_join = on</code></pre>

      <h2>Conclusion</h2>
      <p>PostgreSQL performance tuning is a skill. Start with EXPLAIN ANALYZE, add targeted indexes, and tune configuration — you can achieve 100x speedups on real workloads.</p>
    `
  }
];

// Export for use in controller
if (typeof module !== 'undefined') {
  module.exports = { blogs };
}
